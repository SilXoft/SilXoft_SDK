[production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1
bootstrap.class = "Bootstrap"

;Path
includePaths.library = APPLICATION_PATH "/../library"
bootstrap.path = APPLICATION_PATH "/Bootstrap.php"
resources.layout.layoutPath = APPLICATION_PATH "/layouts/scripts/"
resources.modules[] = ""

;Session
resources.session.gc_maxlifetime = 3000
resources.session.remember_me_seconds = 3000
phpSettings.session.gc_maxlifetime = 3000

;Namespaces
appnamespace = "Application"
autoloaderNamespaces.ext = "Ext_"
autoloaderNamespaces.sl = "Sl_"
autoloaderNamespaces.phpexcel = "PHPExcel_"
autoloaderNamespaces.phpexcel = "Dklab_"

;Resources,plugins
resources.frontController.plugins.eventer = 'Sl_Plugin_Eventer'
resources.frontController.params.prefixDefaultModule = "1"
resources.frontController.params.displayExceptions = 1
resources.frontController.moduleDirectory = APPLICATION_PATH "/Module"

cache.always_clean = 1
;locale
;resources.locale.default = "uk_UA"

;DB session
resources.session.db.name= 'zend_session'
resources.session.db.primary = 'id'
resources.session.db.modifiedColumn = 'modified'
resources.session.db.dataColumn = 'data'
resources.session.db.lifetimeColumn = 'lifetime'

;DB
resources.db.adapter = "PDO_MYSQL"
resources.db.params.host = "localhost"
resources.db.params.username = ""
resources.db.params.password = ""
resources.db.params.dbname = ""
resources.db.params.charset = "utf8"

[staging : production]

[testing : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1

[development : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1
resources.frontController.params.displayExceptions = 1

[translation]
content = APPLICATION_PATH'/../lang'

locale.default = 'ru'

logger.enabled = 0
logger.filename = APPLICATION_PATH'/../logs/translation.log'

[email]
email.server     = smtp.gmail.com
email.username   = **************@gmail.com
email.password   = **************
email.ssl        = ssl
email.port       = 465

[system]
system.umask    = 0;