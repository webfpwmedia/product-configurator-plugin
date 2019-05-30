<?php
use Migrations\AbstractMigration;

class CreateSteps extends AbstractMigration
{

    /**
     * Up.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('steps', ['id' => false, 'primary_key' => ['id']]);

        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('configurator_id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 45,
            'null' => false,
        ]);

        $table->addColumn('sort', 'integer', [
            'default' => 0,
            'limit' => 11,
            'null' => false,
            'signed' => false,
        ]);

        $table->addColumn('config', 'json', [
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

        $this
            ->table('steps')
            ->addForeignKey('configurator_id', 'configurators', 'id')
            ->update();
    }

    /**
     * Down.
     *
     * @return void
     */
    public function down()
    {
        $this
            ->table('steps')
            ->drop()
            ->save();
    }
}
