Portfolio test project (News site)
=============================
Were used:
- Symfony 4
- MySQL
- Doctrine2
- Twig

For start import need create cron command (Run cron job every 3 hours):

    0 */3 * * * php /home/vagrant/code/symfony.local/bin/console import:all > /dev/null