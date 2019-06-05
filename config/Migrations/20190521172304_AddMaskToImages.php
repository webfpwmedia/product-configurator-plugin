<?php
use Migrations\AbstractMigration;

class AddMaskToImages extends AbstractMigration
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
        $table = $this->table('images');
        $table->addColumn('mask', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            'after' => 'name',
        ]);
        $table->update();
    }
}
