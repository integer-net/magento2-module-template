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

    public function __construct(App $app)
    {
        $this->prompt = new Input('>');
        $this->printer = $app->getPrinter();
    }

    public function __invoke(CommandCall $input)
    {
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
            $this->success();
        } catch (\Exception $e) {
            $this->printer->error($e->getMessage());
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
        foreach (Config::getMagentoVersions() as $version => $constraints) {
            if ($this->confirm("Compatible with Magento {$version}?")) {
                $magentoVersions[] = $version;
                $magentoVersionConstraints[] = $constraints;
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

    private function success(): void
    {
        $this->printer->success('All values have been replaced. Your next steps should be:', true);
        $this->removeDevDirectory();
        $this->commitChanges();
        $this->installDependencies();
        $this->connectServices();
    }

    private function removeDevDirectory()
    {
        $this->printer->success(
            implode(
                "\n",
                [
                    "🔸 Remove the dev directory:",
                    "\trm -rf ./dev",
                ]
            )
        );
    }

    private function commitChanges()
    {
        $this->printer->success(
            implode(
                "\n",
                [
                    "🔸 Commit:",
                    "\tgit add -A && git commit",
                ]
            )
        );
    }

    private function installDependencies()
    {
        $this->printer->success(
            implode(
                "\n",
                [
                    "🔸 Install dependencies:",
                    "\tcomposer install --dev",
                ]
            )
        );
    }

    private function connectServices()
    {
        $this->printer->success(
            implode(
                "\n",
                [
                    "🔸 Connect the following services with the Github repository:",
                    "\t- Travis CI to run tests: https://travis-ci.org/",
                    "\t- Scrutinizer for code quality and test coverage: https://scrutinizer-ci.com/",
                    "\t- Code Climate for more code quality: https://codeclimate.com/",
                ]
            )
        );
    }
}
