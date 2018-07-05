<?php

use yii\db\Migration;

/**
 * Class m180704_144540_alter_tavle_user
 */
class m180704_144540_alter_tavle_user extends Migration{

    /**
     * {@inheritdoc}
     */
    public function safeUp(){
        $this->addColumn('{{%user}}','about',$this->text());
        $this->addColumn('{{%user}}','type',$this->integer(3));
        $this->addColumn('{{%user}}','nickname',$this->string(70));
        $this->addColumn('{{%user}}','picture',$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(){
       $this->dropColumn('{{%user}}','about');
       $this->dropColumn('{{%user}}','type');
       $this->dropColumn('{{%user}}','nickname');
       $this->dropColumn('{{%user}}','picture');
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180704_144540_alter_tavle_user cannot be reverted.\n";

        return false;
    }
    */
}
