# Simple Role Syntax
# ==================
# Supports bulk-adding hosts to roles, the primary server in each group
# is considered to be the first unless any hosts have the primary
# property set.  Don't declare `role :all`, it's a meta role.

role :web, %w{deploy@moodle.southdevon.ac.uk deploy@appmoodle.southdevon.ac.uk deploy@onlinelearning.southdevon.ac.uk}
