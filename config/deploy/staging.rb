# Simple Role Syntax
# ==================
# Supports bulk-adding hosts to roles, the primary server in each group
# is considered to be the first unless any hosts have the primary
# property set.  Don't declare `role :all`, it's a meta role.

role :web, %w{deploy@webdev.southdevon.ac.uk}

set :deploy_to, '/srv/moodle2_dev'