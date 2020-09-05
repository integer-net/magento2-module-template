<?php

namespace IntegerNet\ModuleTemplate;

use Minicli\App;
use Minicli\Command\CommandCall;
use Minicli\Input;
use Minicli\Output\OutputHandler;

/**
 * This invokable class is executed from the init script
 */
class Initialize
{
    /**
     * @var Input
     */
    private $prompt;
    /**
     * @var OutputHandler
     */
    private $printer;
    /**
     * @var App
     */
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->prompt = new Input('>');
        $this->printer = $app->getPrinter();
    }

    public function __invoke(CommandCall $input)
    {
        $this->configureFromFlags($input);

        if ($input->hasFlag('--help') || $input->hasFlag('-h') || $input->hasParam('help')) {
            $this->showHelp();
            return;
        }

        try {
            $rootDir = $this->getRootDir($input);
            $this->printer->info("Module directory: $rootDir");

            $values = Config::getDefaultVariables();
            do {
                $values = $this->askValues($values);
                $this->previewValues($values);
            } while (!$this->confirm('Does that look right?'));
            $values += $this->askMagentoCompatibility();

            $this->replaceValues($rootDir, $values);
            $this->removeReadmeSection($rootDir);
            $this->success($values);
        } catch (\Exception $e) {
            $this->printer->error($e->getMessage());
        }
    }

    private function showHelp()
    {
        $README = 'file://' . dirname(__DIR__) . '/README.md';
        $this->printer->info('The init script replaces all placeholders in the template for you interactively');
        $this->printer->info("You can find an explanation of the placeholders in dev/README.md if needed.");
        $this->printer->display("\t{$README}");
        $this->printer->info('Options:');
        $this->printer->info(
            implode(
                "\n",
                [
                    "\t--unicorn\tUse a more colorful theme",
                    "\t--no-ansi\tDo not use colors",
                    "\t--help | -h\tShow this help"
                ]
            )
        );
    }

    private function configureFromFlags(CommandCall $input): void
    {
        if ($input->hasFlag('--unicorn')) {
            $this->app->setTheme('\Unicorn');
            // Registering a theme initializes a new printer instance
            $this->printer = $this->app->getPrinter();
        }
        if ($input->hasFlag('--no-ansi')) {
            $this->printer->clearFilters();
        }
    }

    private function getRootDir(CommandCall $input): string
    {
        $rootDir = ($input->args[2] ?? '');
        if (strpos($rootDir, '/') !== 0) {
            $rootDir = getcwd() . '/' . $rootDir;
        }
        if (realpath($rootDir) === false) {
            throw new \RuntimeException('Path not found: ' . $rootDir);
        }
        return realpath($rootDir);
    }

    private function askValues(array $defaultValues): array
    {
        $values = [];
        foreach ($defaultValues as $key => $default) {
            $this->printer->out(str_replace('-', ' ', ucfirst(substr($key, 1))) . " [$default] ");
            $values[$key] = $this->prompt->read() ?: $default;
        }
        return $values;
    }

    private function previewValues(array $values): void
    {
        $this->printer->info('The following values will be replaced in package files:');
        $this->printer->printTable(
            array_merge(
                [['placeholder', 'value']],
                array_map(fn($key, $value) => [$key, $value], array_keys($values), $values)
            )
        );
    }

    private function confirm(string $question): bool
    {
        do {
            $this->printer->out($question . ' [Y/N] ', 'alt');
            $response = $this->prompt->read();
        } while (trim($response) === '');
        return !(stripos($response, 'Y') !== 0);
    }

    private function askMagentoCompatibility(): array
    {
        $magentoVersions = [];
        $magentoVersionConstraints = [];
        while (empty($magentoVersions)) {
            foreach (Config::getMagentoVersions() as $version => $constraints) {
                if ($this->confirm("Compatible with Magento {$version}?")) {
                    $magentoVersions[] = $version;
                    $magentoVersionConstraints[] = $constraints;
                }
            }
            if (empty($magentoVersions)) {
                $this->printer->error('Please select at least one Magento version');
            }
        }
        $mergedConstraints = array_map('array_unique', array_merge_recursive(...$magentoVersionConstraints));
        return [
            ':php-constraint'       => implode('||', $mergedConstraints['php']),
            ':framework-constraint' => implode('||', $mergedConstraints['magento-framework']),
            ':version-badge'        => implode('%20|%20', $magentoVersions),
        ];
    }

    private function replaceValues(string $rootDir, array $values): void
    {
        $this->printer->info('Replacing values in files:', true);
        foreach (Config::getFilesToUpdate() as $file) {
            $file = $rootDir . '/' . $file;
            $content = file_get_contents($file);
            file_put_contents($file, strtr($content, $values));
            $this->printer->out('✔ ' . substr($file, strlen($rootDir) + 1) . "\n", 'info');
        }
    }

    /**
     * Removes everything until <!-- TEMPLATE --> from README.md
     * @param string $rootDir
     */
    private function removeReadmeSection(string $rootDir)
    {
        $readmeFile = $rootDir . '/README.md';
        $content = file_get_contents($readmeFile);
        $separator = "<!-- TEMPLATE -->\n\n";
        file_put_contents($readmeFile, substr($content, strpos($content, $separator) + strlen($separator)));
        $this->printer->out('✔ ' . "README.md template section removed\n", 'info');
    }

    private function success(array $values): void
    {
        $this->printer->success(
            implode(
                "\n",
                [
                    "┌──────────────────────────────────────────────────────────────┐",
                    "│ 🔽 All values have been replaced. Your next steps should be: │",
                    "└──────────────────────────────────────────────────────────────┘",
                ]
            )
        );
        $this->removeDevDirectory();
        $this->commitChanges();
        $this->connectServices();
        $this->startCoding($values);
        $this->installDependencies();
        $this->printer->success(
            implode(
                "\n",
                [
                    "┌──────────────────────────────────────────────────────────────┐",
                    "│ 🔼 Almost done! Please read your next steps above carefully! │",
                    "└──────────────────────────────────────────────────────────────┘",
                ]
            )
        );
    }

    private function removeDevDirectory()
    {
        $this->printer->out("🔸 Remove the dev directory:", 'info');
        $this->printer->display("\trm -rf ./dev\n");
    }

    private function commitChanges()
    {
        $this->printer->out("🔸 Commit and push initial version:", 'info');
        $this->printer->display("\tgit add -A && git commit -m 'Initial version' && git push origin master\n");
    }

    private function connectServices()
    {
        $this->printer->out(
            implode(
                "\n",
                [
                    "🔸 Connect the following services with the Github repository:",
                    "\t- Packagist to make it available with composer: https://packagist.org/",
                    "\t- Travis CI to run tests: https://travis-ci.org/",
                    "\t- Scrutinizer for code quality and test coverage: https://scrutinizer-ci.com/",
                    "\t- (optional) Code Climate for more code quality metrics: https://codeclimate.com/",
                    "\n",
                ]
            ),
            'info'
        );
    }

    private function startCoding(array $values)
    {
        $this->printer->out("🔸 Install module IN A MAGENTO INSTALLATION to start developing:", 'info');
        $this->printer->display(
            "\tcomposer require --prefer-src {$values[':vendor']}/{$values[':package']} dev-master\n"
        );
        $this->printer->out(
            "🔸 Now you have the Git repository in vendor/{$values[':vendor']}/{$values[':package']}\n\n",
            'info'
        );
    }

    private function installDependencies()
    {
        $this->printer->out(
            "🔸 Install dev dependencies IN THAT DIRECTORY for automatic code quality checks with GrumPHP:",
            'info'
        );
        $this->printer->display("\tcomposer install --dev");
    }
}
