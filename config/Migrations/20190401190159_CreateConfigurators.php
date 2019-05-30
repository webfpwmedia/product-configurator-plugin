<?php
use Migrations\AbstractMigration;

class CreateConfigurators extends AbstractMigration
{

    /**
     * Up.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('configurators', ['id' => false, 'primary_key' => ['id']]);

        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 75,
            'null' => false,
        ]);

        $table->addColumn('bootstrap', 'json', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);

        $table->create();
    }

    /**
     * Down.
     *
     * @return void
     */
    public function down()
    {
        $this
            ->table('configurators')
            ->drop()
            ->save();
    }
}
