set :application, "kaipi.kapsi.fi"
set :domain,      "kaipi@lakka.kapsi.fi"
set :deploy_to,   "/home/users/kaipi/sites/kaipi.kapsi.fi/secure-www/jyps_jasenrekisteri"
set :app_path,    "app"

set :repository,  "https://github.com/kaipi/jyps_jasenrekisteri.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

role :web,        "#{application}"                      # Your HTTP server, Apache/etc
role :app,        "#{application}"             # This may be the same as your `Web` server

set  :keep_releases,  3
set :use_sudo, false

set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]
set :use_composer, true

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL
