// resources/js/app.js

import './bootstrap';

// AdminLTE Core
import 'admin-lte';

// Bootstrap Bundle (required by AdminLTE)
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap';


// jQuery + Moment.js (required for DateRangePicker)
import $ from 'jquery';
import moment from 'moment';

// DateRangePicker
import 'daterangepicker/daterangepicker.js';
import 'daterangepicker/daterangepicker.css';

// Init DateRangePicker example
$(function () {
      $('#reservation').daterangepicker({
            opens: 'left',
            locale: {
                  format: 'YYYY-MM-DD'
            }
      });
});
