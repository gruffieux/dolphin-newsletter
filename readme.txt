Multilingual Newsletter


Version 1.0.0
Compatible with Dolphin 7.1

IMPORTANT: This is a commercial product made by MensGo and cannot be modified other than personal use.
This product cannot be redistributed for free or a fee without written permission from MensGo.

LEGAL INFORMATION: The goal of this product is to communicate relevant informations to a list of subscribed members.
All contents sent with this product must include an unsubscribe link.
MensGo will not take any responsabilities of SPAM or abusive use of this product.


Installation manual
-------------------

1. Uncompress archive and copy newsletter/ folder into modules/mensgo/ folder (create mensgo/ folder if not exist).
2. Into newsletter/ folder, create save/ folder with all rights (777).
3. Go to administration, modules management and install Newsletter (by MensGo).


User manual
-----------

To understand this doc:
Campaign = object of the newsletter, content = what we sent, member = where we send

How to create and test a newsletter:
1. Create a campaign with a name, a description and a limit date.
2. Create a content with a title. Use TinyMCE helper or copy your HTML code directly. You can replace the default unsubscribe link by your own BUT YOU MUST PUT AN UNSUBSCRIBE LINK TO BE LEGAL. Finally set a language.
3. Create contents for each language you need.
4. Create members with e-mail, name, country, language, etc... You can import your member list from a CSV file. For more details, see info tips in the import section.
5. Back to campaigns, associate campaign with your contents and members (see tools next to your campaign).
6. Click the round button to change status of your campaign to ready (green).
7. Go to settings, check test e-mail and set a test e-mail.
8. Back to campaigns, click send button. Campaigns should have changed status to sent (blue).
9. Check your e-mail to preview the result. You can see also logs into the save/ folder.
10. When you are ready to send to real members, uncheck test e-mail from settings.

How to change newsletter frequency:
You must change the cron data manually in database.
Open sys_cron_jobs table, search cron named Newsletter and change time value as you wish.
More infos: http://fr.wikipedia.org/wiki/Cron

How to activate click tracking (require development knowledges):
1. Open _footer.html template, add code below just after <bx_injection:injection_footer /> tag.
<!--Stats mailing newsletter-->
__custom_call__
2. Open BxTemplFunction.php, add code below into TemplPageAddComponent method.
case 'custom_call':
    if (isset($_GET['mg_newsl_cid'])) {
        $aParts = explode('_', $_GET['mg_newsl_cid'], 2);
        if (!empty($aParts)) {
            BxDolService::call('newsletter', 'stats', array($aParts[0], $aParts[1], empty($_GET['click']) ? 'read' : 'click'));
        }
    }
3. Add mg_newsl_cid parameter in URL of your content yout want to track.
CID is composed with ID of campaign, one underline and the member hash.


Optionnal settings
-------------------

Use test e-mail:
To simulate a send of your newsletters to a unique destination e-mail.
Will send maximum 1 e-mail per language for each campaign.

Test e-mail:
Destination e-mail for simulation.


IMAP settings below are used for bots prevention.

IMAP box:
String to connect to your IMAP mail.

IMAP user:
User to connect to your IMAP mail.

IMAP password:
Password to connect to your IMAP mail.

Subject mail returned when failure (regular expression):
List of returned mail subjects that are reckon as bot e-mail.
Bot score is then incremented by 1 for this e-mail during a Bot Check.



Enjoy!
MensGo Team
