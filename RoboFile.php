<?php
require_once 'vendor/autoload.php';

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * NOTE: All arguments referencing $site and $dest will default to looking
 * for the peridot-php.github.io directory in the same directory as the Peridot
 * project.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    /**
     * Release a new version of Peridot.
     *
     * @param null|string $version the new version number
     * @param null|string $site path to the Peridot site to build
     */
    public function release($version = null, $site = null)
    {
        $this->yell("Releasing Peridot");

        $this->taskExec("git pull upstream master")->run();

        $bump = $this->version($version);
        $this->phar($site);
        $this->docs($site);


        $this->taskExec('git commit -am "release ' . $bump . '"')->run();
        $this->taskExec('git tag -a ' . $bump . ' -m "release ' . $bump . '"')->run();
        $this->taskExec("git push upstream $bump")->run();
        $this->taskExec("git push upstream master")->run();

        $this->publish($site);
    }

    /**
     * @param $change
     */
    public function changed($change)
    {
        $this->taskChangelog()
            ->version(\Peridot\Console\Version::NUMBER)
            ->change($change)
            ->run();
    }

    /**
     * Bump the version of Peridot. Defaults to a minor release.
     *
     * @param null $version
     * @return null|string
     */
    public function version($version = null)
    {
        if (!$version) {
            $versionParts = explode('.', \Peridot\Console\Version::NUMBER);
            $versionParts[count($versionParts)-1]++;
            $version = implode('.', $versionParts);
        }
        $versionFile = __DIR__ . '/src/Console/Version.php';
        $this->taskReplaceInFile($versionFile)
            ->from('NUMBER = "' . \Peridot\Console\Version::NUMBER . '"')
            ->to('NUMBER = "' . $version . '"')
            ->run();

        return $version;
    }

    /**
     * Generate docs via apigen then move them to the
     * site folder
     *
     * @param null|string $dest
     */
    public function docs($dest = null)
    {
        if (!$dest) {
            $dest = __DIR__ . '/../peridot-php.github.io';
        }
        $src = __DIR__ . '/docs';
        $this->taskDeleteDir([
            $dest . '/docs',
            $src
        ])->run();
        $this->taskExec('apigen generate')->run();
        $this->taskCopyDir([
            $src => $dest . '/docs'
        ])->run();
        $this->taskDeleteDir([$src])->run();
    }

    /**
     * Build a phar and move it to the site folder
     *
     * @param null|string $dest
     */
    public function phar($dest = null)
    {
        $this->taskComposerInstall()
            ->printed(false)
            ->noDev()
            ->run();

        if (!$dest) {
            $dest = __DIR__ . '/../peridot-php.github.io';
        }
        $src = __DIR__ . '/peridot.phar';
        $this->taskCleanDir([$dest . '/downloads'])->run();
        if (file_exists($src)) {
            unlink($src);
        }
        $this->taskExec('box build')->run();
        $destPhar = $dest . '/downloads/peridot.phar';
        $this->say("copying phar to $destPhar");
        rename($src, $destPhar);
    }

    /**
     * Publish the Peridot site
     *
     * @param null|string $dest
     */
    public function publish($dest = null)
    {
        $this->yell('Publishing Peridot docs');
        if (!$dest) {
            $dest = __DIR__ . '/../peridot-php.github.io';
        }
        $cwd = getcwd();
        chdir($dest);
        $this->taskGitStack()
            ->add('-A')
            ->commit('auto-update')
            ->pull()
            ->push()
            ->run();
        chdir($cwd);
    }

    /**
     * Rebuild docs, phar, and publish the site
     *
     * @param null|string $dest
     */
    public function site($dest = null)
    {
        if (!$dest) {
            $dest = __DIR__ . '/../peridot-php.github.io';
        }
        $this->phar($dest);
        $this->docs($dest);
        $this->publish($dest);
    }
}
