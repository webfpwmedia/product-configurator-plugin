<?php
use Migrations\AbstractMigration;

class AddImageMaskToComponents extends AbstractMigration
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
        $table->addColumn('image_mask', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            'after' => 'mask',
        ]);
        $table->update();
    }
}
