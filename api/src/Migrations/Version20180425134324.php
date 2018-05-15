<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180425134324 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE element ADD story_thread_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E391B2A7A68 FOREIGN KEY (story_thread_id) REFERENCES story_thread (id)');
        $this->addSql('CREATE INDEX IDX_41405E391B2A7A68 ON element (story_thread_id)');
        $this->addSql('ALTER TABLE story_thread ADD current_arc_segment_id INT NOT NULL');
        $this->addSql('ALTER TABLE story_thread ADD CONSTRAINT FK_7E23FE71D8FD3678 FOREIGN KEY (current_arc_segment_id) REFERENCES arc_segment (id)');
        $this->addSql('CREATE INDEX IDX_7E23FE71D8FD3678 ON story_thread (current_arc_segment_id)');
        $this->addSql('ALTER TABLE story_line ADD story_thread_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE story_line ADD CONSTRAINT FK_47B9D8D81B2A7A68 FOREIGN KEY (story_thread_id) REFERENCES story_thread (id)');
        $this->addSql('CREATE INDEX IDX_47B9D8D81B2A7A68 ON story_line (story_thread_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E391B2A7A68');
        $this->addSql('DROP INDEX IDX_41405E391B2A7A68 ON element');
        $this->addSql('ALTER TABLE element DROP story_thread_id');
        $this->addSql('ALTER TABLE story_line DROP FOREIGN KEY FK_47B9D8D81B2A7A68');
        $this->addSql('DROP INDEX IDX_47B9D8D81B2A7A68 ON story_line');
        $this->addSql('ALTER TABLE story_line DROP story_thread_id');
        $this->addSql('ALTER TABLE story_thread DROP FOREIGN KEY FK_7E23FE71D8FD3678');
        $this->addSql('DROP INDEX IDX_7E23FE71D8FD3678 ON story_thread');
        $this->addSql('ALTER TABLE story_thread DROP current_arc_segment_id');
    }
}
