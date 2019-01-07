<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190107204728 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE MemberFee CHANGE memo memo VARCHAR(255) DEFAULT NULL, CHANGE email_sent email_sent TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE User CHANGE salt salt VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE Member CHANGE member_type_id member_type_id INT DEFAULT NULL, CHANGE country country VARCHAR(60) DEFAULT NULL, CHANGE email email VARCHAR(60) DEFAULT NULL, CHANGE referer_person_name referer_person_name VARCHAR(60) DEFAULT NULL, CHANGE memo memo VARCHAR(255) DEFAULT NULL, CHANGE membership_end_date membership_end_date DATE DEFAULT NULL, CHANGE telephone telephone VARCHAR(255) DEFAULT NULL, CHANGE gender gender TINYINT(1) DEFAULT NULL, CHANGE selfcare_password selfcare_password VARCHAR(255) DEFAULT NULL, CHANGE selfcare_password_salt selfcare_password_salt VARCHAR(255) DEFAULT NULL, CHANGE join_form_freeword join_form_freeword VARCHAR(255) DEFAULT NULL, CHANGE birth_year birth_year INT DEFAULT NULL, CHANGE second_name second_name VARCHAR(30) DEFAULT NULL, CHANGE reminder_sent_date reminder_sent_date DATE DEFAULT NULL, CHANGE next_memberfee_paid next_memberfee_paid TINYINT(1) DEFAULT NULL, CHANGE ParentMemberId ParentMemberId INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Member CHANGE member_type_id member_type_id INT DEFAULT NULL, CHANGE second_name second_name VARCHAR(30) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE country country VARCHAR(60) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(60) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE referer_person_name referer_person_name VARCHAR(60) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE memo memo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE membership_end_date membership_end_date DATE DEFAULT \'NULL\', CHANGE birth_year birth_year INT DEFAULT NULL, CHANGE telephone telephone VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE gender gender TINYINT(1) DEFAULT \'NULL\', CHANGE selfcare_password selfcare_password VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE selfcare_password_salt selfcare_password_salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE join_form_freeword join_form_freeword VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE reminder_sent_date reminder_sent_date DATE DEFAULT \'NULL\', CHANGE next_memberfee_paid next_memberfee_paid TINYINT(1) DEFAULT \'NULL\', CHANGE ParentMemberId ParentMemberId INT DEFAULT NULL');
        $this->addSql('ALTER TABLE MemberFee CHANGE memo memo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE email_sent email_sent TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE User CHANGE salt salt VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
    }
}
