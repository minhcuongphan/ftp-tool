<style>
body {
  position: relative;
  font-family: "lucida grande", "Segoe UI", Arial, sans-serif;
  font-size: 14px;
  width: 1024;
  padding: 1em;
  margin: 0;
  min-height: 100%;
}
th {
  font-weight: normal;
  color: #1f75cc;
  background-color: #eee;
  padding: 0.5em 1em 0.5em 0.2em;
  text-align: left;
  cursor: pointer;
  user-select: none;
}
th .indicator {
  margin-left: 6px;
}
thead {
  border-top: 1px solid #82cffa;
  border-bottom: 1px solid #96c4ea;
  border-left: 1px solid #e7f2fb;
  border-right: 1px solid #e7f2fb;
}
#top {
  height: 52px;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    height: 800px;
    width: 100%;
    z-index: 8;
    background-color: rgba(0, 0, 0, 0.6);
}

#item_popup {
    display: none;
    position: absolute;
    background: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    width: 110px;
    height: 120px;
    top: 520px;
    left: 410px;
    box-shadow: 2px 2px 5px #aaa;
    z-index: 1000;
}

#item_popup p:hover {
  background-color: #eee;
  cursor: pointer;
}

#item_popup a {
  text-align: left;
}

.search_section {
    float: right;
}

button.search_button {
    background-color: #eeeeee;
    border-radius: 2px;
    border: 1px solid black;
}

label {
  display: block;
  font-size: 11px;
  color: #555;
}
#file_drop_target {
  width: 500px;
  padding: 12px 0;
  border: 4px dashed #ccc;
  font-size: 12px;
  color: #ccc;
  text-align: center;
  float: right;
  margin-right: 20px;
}
#file_drop_target.drag_over {
  border: 4px dashed #96c4ea;
  color: #96c4ea;
}
#upload_progress {
  padding: 4px 0;
}
#upload_progress .error {
  color: #a00;
}
#upload_progress > div {
  padding: 3px 0;
}
.no_write #file_drop_target {
  display: none;
}
.progress_track {
  display: inline-block;
  width: 200px;
  height: 10px;
  border: 1px solid #333;
  margin: 0 4px 0 10px;
}
.progress {
  background-color: #82cffa;
  height: 10px;
}
footer {
  font-size: 11px;
  color: #bbbbc5;
  padding: 4em 0 0;
  text-align: left;
}
footer a,
footer a:visited {
  color: #bbbbc5;
}
#breadcrumb {
  padding-top: 34px;
  font-size: 15px;
  color: #aaa;
  display: inline-block;
  float: left;
  width: 100%;
}

.breadcrumb_items {
    background: #eee;
    padding: 10px 4px;
}

#folder_actions {
  width: 50%;
  float: right;
}
a,
a:visited {
  color: #00c;
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}
.sort_hide {
  display: none;
}
table {
  border-collapse: collapse;
  width: 100%;
}
thead {
  max-width: 1024px;
}
td {
  padding: 0.2em 1em 0.2em 0.2em;
  border-bottom: 1px solid #def;
  height: 30px;
  font-size: 12px;
  white-space: nowrap;
}
td.first {
  font-size: 14px;
  white-space: normal;
}
td.empty {
  color: #777;
  font-style: italic;
  text-align: center;
  padding: 3em 0;
}
.is_dir .size {
  color: transparent;
  font-size: 0;
}
.is_dir .size:before {
  content: "--";
  font-size: 14px;
  color: #333;
}
.is_dir .download {
  visibility: hidden;
}
a.delete {
  display: inline-block;
  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADtSURBVHjajFC7DkFREJy9iXg0t+EHRKJDJSqRuIVaJT7AF+jR+xuNRiJyS8WlRaHWeOU+kBy7eyKhs8lkJrOzZ3OWzMAD15gxYhB+yzAm0ndez+eYMYLngdkIf2vpSYbCfsNkOx07n8kgWa1UpptNII5VR/M56Nyt6Qq33bbhQsHy6aR0WSyEyEmiCG6vR2ffB65X4HCwYC2e9CTjJGGok4/7Hcjl+ImLBWv1uCRDu3peV5eGQ2C5/P1zq4X9dGpXP+LYhmYz4HbDMQgUosWTnmQoKKf0htVKBZvtFsx6S9bm48ktaV3EXwd/CzAAVjt+gHT5me0AAAAASUVORK5CYII=)
    no-repeat scroll 0 2px;
  color: #d00;
  margin-left: 15px;
  font-size: 11px;
  padding: 0 0 0 13px;
}
.name {
  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAABAklEQVRIie2UMW6DMBSG/4cYkJClIhauwMgx8CnSC9EjJKcwd2HGYmAwEoMREtClEJxYakmcoWq/yX623veebZmWZcFKWZbXyTHeOeeXfWDN69/uzPP8x1mVUmiaBlLKsxACAC6cc2OPd7zYK1EUYRgGZFkG3/fPAE5fIjcCAJimCXEcGxKnAiICERkSIcQmeVoQhiHatoWUEkopJEkCAB/r+t0lHyVN023c9z201qiq6s2ZYA9jDIwx1HW9xZ4+Ihta69cK9vwLvsX6ivYf4FGIyJj/rg5uqwccd2Ar7OUdOL/kPyKY5/mhZJ53/2asgiAIHhLYMARd16EoCozj6EzwCYrrX5dC9FQIAAAAAElFTkSuQmCC)
    no-repeat scroll 0px 12px;
  padding: 15px 0 10px 40px;
}
.is_dir .name {
  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADdgAAA3YBfdWCzAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAI0SURBVFiF7Vctb1RRED1nZu5977VQVBEQBKZ1GCDBEwy+ISgCBsMPwOH4CUXgsKQOAxq5CaKChEBqShNK222327f79n0MgpRQ2qC2twKOGjE352TO3Jl76e44S8iZsgOww+Dhi/V3nePOsQRFv679/qsnV96ehgAeWvBged3vXi+OJewMW/Q+T8YCLr18fPnNqQq4fS0/MWlQdviwVqNpp9Mvs7l8Wn50aRH4zQIAqOruxANZAG4thKmQA8D7j5OFw/iIgLXvo6mR/B36K+LNp71vVd1cTMR8BFmwTesc88/uLQ5FKO4+k4aarbuPnq98mbdo2q70hmU0VREkEeCOtqrbMprmFqM1psoYAsg0U9EBtB0YozUWzWpVZQgBxMm3YPoCiLpxRrPaYrBKRSUL5qn2AgFU0koMVlkMOo6G2SIymQCAGE/AGHRsWbCRKc8VmaBN4wBIwkZkFmxkWZDSFCwyommZSABgCmZBSsuiHahA8kA2iZYzSapAsmgHlgfdVyGLTFg3iZqQhAqZB923GGUgQhYRVElmAUXIGGVgedQ9AJJnAkqyClCEkkfdM1Pt13VHdxDpnof0jgxB+mYqO5PaCSDRIAbgDgdpKjtmwm13irsnq4ATdKeYcNvUZAt0dg5NVwEQFKrJlpn45lwh/LpbWdela4K5QsXEN61tytWr81l5YSY/n4wdQH84qjd2J6vEz+W0BOAGgLlE/AMAPQCv6e4gmWYC/QF3d/7zf8P/An4AWL/T1+B2nyIAAAAASUVORK5CYII=)
    no-repeat scroll 0px 10px;
  padding: 15px 0 10px 40px;
}
.download {
  background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAB2klEQVR4nJ2ST2sTQRiHn5mdmj92t9XmUJIWJGq9NHrRgxQiCtqbl97FqxgaL34CP0FD8Qv07EHEU0Ew6EXEk6ci8Q9JtcXEkHR3k+zujIdUqMkmiANzmJdnHn7vzCuIWbe291tSkvhz1pr+q1L2bBwrRgvFrcZKKinfP9zI2EoKmm7Azstf3V7fXK2Wc3ujvIqzAhglwRJoS2ImQZMEBjgyoDS4hv8QGHA1WICvp9yelsA7ITBTIkwWhGBZ0Iv+MUF+c/cB8PTHt08snb+AGAACZDj8qIN6bSe/uWsBb2qV24/GBLn8yl0plY9AJ9NKeL5ICyEIQkkiZenF5XwBDAZzWItLIIR6LGfk26VVxzltJ2gFw2a0FmQLZ+bcbo/DPbcd+PrDyRb+GqRipbGlZtX92UvzjmUpEGC0JgpC3M9dL+qGz16XsvcmCgCK2/vPtTNzJ1x2kkZIRBSivh8Z2Q4+VkvZy6O8HHvWyGyITvA1qndNpxfguQNkc2CIzM0xNk5QLedCEZm1VKsf2XrAXMNrA2vVcq4ZJ4DhvCSAeSALXASuLBTW129U6oPrT969AK4Bq0AeWARs4BRgieMUEkgDmeO9ANipzDnH//nFB0KgAxwATaAFeID5DQNatLGdaXOWAAAAAElFTkSuQmCC)
    no-repeat scroll 0px 5px;
  padding: 4px 0 4px 20px;
}

.actions {
  float: left;
  margin-left: 0px;
  width: 100%;
  margin-top: 15px;
  margin-bottom: 11px;
}

#upload_label {
    display: inline-block;
    color: black;
    font-size: 15px;
    padding-top: 10px;
}

/* Form create new folder */
.open-button {
  background-color: #555;
  color: white;
  padding: 16px 20px;
  border: none;
  cursor: pointer;
  opacity: 0.8;
  position: fixed;
  bottom: 23px;
  right: 28px;
  width: 280px;
}

/* The popup form - hidden by default */
.form-popup {
    position: fixed;
    top: 50%;
    left: 60%;
    transform: translate(-50%, -50%);
    width: 50%;
    z-index: 9;
}

/* Add styles to the form container */
.form-container {
  max-width: 300px;
  padding: 10px;
  background-color: white;
}

/* Full-width input fields */
.form-container input[type=text], .form-container input[type=password] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  border: none;
  background: #f1f1f1;
}

/* When the inputs get focus, do something */
.form-container input[type=text]:focus, .form-container input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}

/* Set a style for the submit/login button */
.form-container .btn {
  background-color: #04AA6D;
  color: white;
  padding: 16px 20px;
  border: none;
  cursor: pointer;
  width: 100%;
  margin-bottom:10px;
  opacity: 0.8;
}

/* Add a red background color to the cancel button */
.form-container .cancel {
  background-color: red;
}

/* Add some hover effects to buttons */
.form-container .btn:hover, .open-button:hover {
  opacity: 1;
} */

/* End form create new folder */
</style>