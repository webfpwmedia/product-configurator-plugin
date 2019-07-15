<?php
use Migrations\AbstractMigration;

class RemoveConfigurationIdFromComponents extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('components');

        $table->dropForeignKey('configurator_id');
        $table->update();

        $table->removeColumn('configurator_id');
        $table->update();
    }
}
