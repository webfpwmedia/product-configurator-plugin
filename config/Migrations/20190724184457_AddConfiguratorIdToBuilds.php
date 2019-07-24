<?php

use Cake\ORM\Locator\LocatorAwareTrait;
use Migrations\AbstractMigration;

class AddConfiguratorIdToBuilds extends AbstractMigration
{
    use LocatorAwareTrait;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $buildsTable = $this->getTableLocator()->get('ARC/ProductConfigurator.Builds');
        $buildsTable->deleteAll(['1=1']);

        $table = $this->table('builds');
        $table->addColumn('configurator_id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);
        $table->update();

        $table->addForeignKey('configurator_id', 'configurators');
        $table->update();
    }
}
