<?php
use Migrations\AbstractMigration;

class CreateComponents extends AbstractMigration
{

    /**
     * Up.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('components', ['id' => false, 'primary_key' => ['id']]);

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

        $table->addColumn('mask', 'string', [
            'default' => null,
            'limit' => 255,
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
            ->table('components')
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
            ->table('components')
            ->drop()
            ->save();
    }
}
