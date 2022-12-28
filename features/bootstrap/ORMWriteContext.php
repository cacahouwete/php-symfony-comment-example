<?php

use Behat\MinkExtension\Context\RawMinkContext;

class ORMWriteContext extends RawMinkContext
{
    /**
     * @BeforeSuite
     */
    public static function initDatabase(): void
    {
        \shell_exec('/srv/bin/console doctrine:database:drop --if-exists --force --env=test');
        \shell_exec('/srv/bin/console doctrine:database:create --if-not-exists --env=test');
        \shell_exec('/srv/bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --quiet --env=test');
        \shell_exec('/srv/bin/console hautelook:fixtures:load --no-interaction --quiet --env=test');
        \shell_exec('/srv/bin/console doctrine:query:sql "DROP DATABASE IF EXISTS comment_test_template" --env=test');
        \shell_exec('/srv/bin/console doctrine:query:sql "CREATE DATABASE comment_test_template WITH TEMPLATE cmi_test" --env=test');
        \shell_exec('/srv/bin/console doctrine:database:create --if-not-exists --env=dev');
    }

    /**
     * @BeforeFeature @database&&~@fixtures
     * @BeforeScenario @database&&~@fixtures&&@clean
     */
    public static function createSchema(): void
    {
        \shell_exec('/srv/bin/console doctrine:database:drop --if-exists --force --env=test');
        \shell_exec('/srv/bin/console doctrine:database:create --if-not-exists --env=test');
        \shell_exec('/srv/bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --quiet --env=test');
    }

    /**
     * @BeforeFeature @database&&@fixtures
     * @BeforeScenario @database&&@fixtures&&@clean
     */
    public static function loadFixtures(): void
    {
        \shell_exec('/srv/bin/console doctrine:database:drop --if-exists --force --env=test');
        \shell_exec('/srv/bin/console doctrine:query:sql "CREATE DATABASE comment_test WITH TEMPLATE cmi_test_template" --env=dev');
    }
}
