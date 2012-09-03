# A sample Guardfile
# More info at https://github.com/guard/guard#readme

guard 'livereload', :apply_js_live => false do
  watch(%r{frontend/www/themes/v2/assets/.+\.(css|js)})
  watch(%r{frontend/www/themes/v2/views/layouts/main.php})
  watch(%r{frontend/modules/v2/views/.+\.php})
end

guard 'coffeescript', :input => 'frontend/www/themes/v2/assets/js', :bare => true
