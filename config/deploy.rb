if ENV['environment'] == "production"
    set :application, "moodle2"
    role :app,  "moodle.southdevon.ac.uk","commoodle.southdevon.ac.uk","appmoodle.southdevon.ac.uk"
    role :web,  "moodle.southdevon.ac.uk"
    role :db,   "moodle.southdevon.ac.uk", :primary => true
    set :keep_releases, 5
else
    set :application, "moodle2_dev"
    role :app,  "webdev.southdevon.ac.uk"
    role :web,  "webdev.southdevon.ac.uk"
    role :db,   "webdev.southdevon.ac.uk", :primary => true
    set :keep_releases, 3
end

default_run_options[:pty] = true

set :repository,"git@github.com:sdc/moodle.git"
set :branch,    "sdc_24"
set :deploy_to, "/srv/#{application}"
set :scm, :git

namespace :deploy do
    %W(start stop restart migrate finalize_update).each do |event|
        task event do
            # don't
        end
    end
end

task :after_deploy do
    run "cp #{shared_path}/config.php #{current_path}/"
    run "echo \\$CFG-\\>dirroot  = \\'#{release_path}\\'\\; >> #{current_path}/config.php"
    run "echo require_once\\(\\'#{release_path}/lib/setup.php\\'\\)\\; >> #{current_path}/config.php"
    run "echo ?\\> >> #{current_path}/config.php"
    # copy SDC Three theme images from shared folder to theme folder
    run "cp #{shared_path}/sdcthree_theme_images/* #{current_path}/theme/sdcthree/pix/graphics/"
end
