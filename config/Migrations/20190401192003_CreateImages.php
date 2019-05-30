<?php
use Migrations\AbstractMigration;

class CreateImages extends AbstractMigration
{

    /**
     * Up.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('images', ['id' => false, 'primary_key' => ['id']]);

        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);

        $table->addColumn('position', 'string', [
            'default' => null,
            'limit' => 45,
            'null' => false,
        ]);

        $table->addColumn('layer', 'integer', [
            'default' => 0,
            'limit' => 11,
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
            ->table('images')
            ->drop()
            ->save();
    }
}
