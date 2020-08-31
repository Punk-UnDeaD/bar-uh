import '../components/x-class-toggler'
import '../components/x-auto-toggler'
import '../components/x-js-button'
import '../components/x-auto-save-button'
import '../components/x-icon/x-icon'
import '../components/x-picture'
import '../components/x-file-drop'
import '../components/x-messages/x-messages'

require('./reset.scss')
require('./admin/theme.scss')
require('./typography.scss')
require('./admin/layout.scss')
require('./admin/sidebar.scss')
require('./admin/breadcrumb.scss')
require('./admin/form.scss')
require('./admin/pager.scss')
require('./admin/alert.scss')
require('./admin/actions.scss')
require('./admin/content.scss')
require('./admin/picture.scss')

document.addEventListener('DOMContentLoaded', function () {
  setTimeout(() => this.querySelector('body').classList.add('ready'), 16);
});