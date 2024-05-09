(function($) {
  surveyController = {
    init: function(t, e) {
      this.jQuery = $ = t,
      this.$form = e,
      this.$apiLoader = e.find("#apiLoader"),
      this.baseURL = `${CFG.wwwroot}/app_rest_api/offline/index.php`,
      this.surveyid = 0,
      this.eventid = 0,
      this.loadedsurvey = null,
      this.$qplayer_data = null,
      this.$qplayer_question_current = 0,
      this.$qplayer_question_total = 0,
      this.$qplayer = e.find("[qplayer]"),
      this.$qplayer_header = e.find("[qplayer] [qplayerheader]"),
      this.$qplayer_question_title = e.find("[qplayer] [questiontitle]"),
      this.$qplayer_question_text = e.find("[qplayer] [questiontext]"),
      this.$qplayer_question_submit = e.find("[qplayer] [questionsubmit]");
      this.$qplayer_question_prev = e.find("[qplayer] [prevquestion]");
      this.$qplayer_question_next = e.find("[qplayer] [nextquestion]");
      this.$qplayer_userresponse = {};

      this._initListeners(),
      console.log("quizController init: ", this);
      // console.log("quizController init: ");
    },
    _initListeners: function(){
      var that = this;
      that.surveyid = this.$form.data("id"),
      that.eventid = this.$form.data("eventid"),
      this.$form.on("click", "[questionsubmit]", function(e) {that._questionsubmit()}),
      that._getSurvey();
      console.log("quizController that: ", that);
      // console.log("quizController that: ", this);
    },
    _getSurvey: function() {
      var that = this;
      this._APICall(
        this._prepareRequest(
          "getSurveyDetails",
          {
            surveyid:that.surveyid,
            eventid:that.eventid
          }
        ),
        function (result) {
          console.log("getSurveyDetails result: ", result);
          if(result.code == 200){
            that.loadedsurvey = result.data;
            if(result.data.userresponse){
              that.$qplayer_userresponse = result.data.userresponse;
            }
            console.log("that.loadedsurvey- ", that.loadedsurvey);
            that._startsurvey();
          } else {
              displayToast("Error", "Please Try again...", "error");
          }
        }
      );
    },
    _startsurvey: function() {
      if(this.loadedsurvey){
        var that = this;
        this.$apiLoader.addClass("active");
        that.$qplayer_data = this.loadedsurvey,
        that.$qplayer_question_current = 0;
        that.$qplayer_question_total = this.loadedsurvey.questions.length;
        console.log("that: ", that);
        if(that.$qplayer_question_total > 0){
          console.log("qpdata------- ", that.$qplayer_data);
          that._bindPlayerElements();
          that._loadquestion();
        }
        console.log("Survey to start", this.loadedsurvey);
      } else {
        displayToast("Falied", "Failed to load survey", "error");
      }
    },
    prepareSurveyPlayer: function(surveyid, eventid=0) {
      return `<div class="surveymodule" surveyplayer data-id="${surveyid}" data-eventid="${eventid}">
                <div class="qplayer" qplayer>
                  <div class="qplayer-header" qplayerheader>
                  </div>
                  <div class="qplayer-body">
                    <div class="qplayer-body-main">
                      <div questioncontainer>
                        <div questionbody>
                          <div class="qplayer-header-question-title" questiontitle></div>
                          <div class="qplayer-body-main-questiontext" questiontext></div>
                        </div>
                      </div>
                      <div class="qplayer-body-main-questionbottom">
                        <button class="btnquiz qplayer-body-main-questionbottom-btn" style="display:none;" questionsubmit  data-langplace="text" data-langstring="language_question_player_save">Save Answer</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="apiLoader" class="apiLoader active">
                  <img src="/images/ajax-loader-white.gif"/>
                </div>
              </div>`;
    },
    _bindPlayerElements: function() {
      this.$qplayer = this.$form.find("[qplayer]"),
      this.$qplayer_header = this.$form.find("[qplayer] [qplayerheader]"),
      this.$qplayer_question_body = this.$form.find("[qplayer] [questionbody]"),
      this.$qplayer_question_submit = this.$form.find("[qplayer] [questionsubmit]");
    },
    _loadquestion: function() {
      this.$apiLoader.removeClass("active");
      var currentquestion = this.$qplayer_data.questions;
      var that = this;
      if(currentquestion){
        console.log("currentquestion- ", currentquestion)
        var allquestions = '';
        currentquestion.forEach(function(value, key) {
          var questionDataResponse=that._prepareQuestion(value);
          allquestions += `<div questionbody${value.id}>
            <div class="qplayer-header-question-title" questiontitle>${value.questiontext}</div>
            <div class="qplayer-body-main-questiontext" questiontext>${questionDataResponse}</div>
          </div>`;
        });
        this.$qplayer_question_body.html(allquestions)
        this.$qplayer_question_body.find("img[src^='https://fivestudents.s3']").addClass("latex-s3")
        this.$qplayer_question_submit.show();
      } else {
          displayToast("Error", "Unable to find question", "error");
      }
    },  
    _prepareQuestion: function(question) {
      console.log("question- ", question);
      var that=this;
      let questionTemplate='';
      questionTemplate+=`${question.questiondescription} <br></br>`;
      var questionstatus = "";
      var userresponse = "";
      var isattempted = false;

      if(this.$qplayer_userresponse && this.$qplayer_userresponse[question.id] != undefined){
        isattempted = true;
        userresponse = this.$qplayer_userresponse[question.id];
      }
      switch(question.questiontype) {
        case "shortanswer":
          var answerstatus="";
          var readonly="";
          if(isattempted){
            readonly="disabled";
          }
          let shortanswers=`<div><label >Answer: </label><input class="${answerstatus}" ${readonly} type="text" name="answer${question.id}" data-element="answer" data-elementtye="shortanswer" value="${userresponse}"></div>`;
          questionTemplate+=shortanswers;
        break;
        case "singlechoice":
        case "truefalse":
          let radioboxs='';
          let optionclass='';
          question.options.forEach(function(radiobox){
            var optionclass="";
            var readonly="";
            if(isattempted){
              readonly="disabled";
              if(userresponse == radiobox.id){
                readonly +=" checked";
              }
            }
            radioboxs+=`<div class="multi_choice">
            <label>
                <input type="radio" class="${optionclass}" ${readonly} name="multichoice${question.id}"  data-element="answer" data-elementtye="multichoice" value="${radiobox.id}"> ${radiobox.option}
            </label>
            </div>`;
          });
          questionTemplate+=radioboxs;
        break;
        case "multichoice":
          let checkboxs='';
          question.options.forEach(function(checkbox){
            var optionclass="";
            var readonly="";
            if(isattempted){
              readonly="disabled";
              if(userresponse.includes(checkbox.id)){
                readonly +=" checked";
              }
            }
            checkboxs+=`<div><label><input type="checkbox" class="${optionclass}" ${readonly} name="multiselect${question.id}[]"  data-element="answer" data-elementtye="multiselect" value="${checkbox.id}"> ${checkbox.option}</label></div>`;
          });
          questionTemplate+=checkboxs;
        break;
      }
      return questionTemplate;
    },
    
    _questionsubmit: function() {
      var that=this;
      that.$apiLoader.addClass("active");
      var allquestion = this.$qplayer_data.questions;
      var allvalidation = [];
      allquestion.forEach(function(question, key) {
        var q_id = question.id;
        var q_type =   question.questiontype;
        var q_answer=undefined;
        var myarray = [];
        var validanswer = true;
        console.log("Quesstion : ",question);
        switch (q_type) {
          case "truefalse":
          case "singlechoice":
          case "shortanswer":
            var q_answer1 = that.$qplayer_question_body.find(`[questionbody${q_id}] [data-element="answer"]:checked`).val();
            console.log("q_answer1---- ", q_answer1)
            if(!q_answer1){ 
              if(question.required == 1){
                validanswer = false; 
                allvalidation.push(validanswer);
              }
            } else {
              q_answer = q_answer1;
            }
          break;
  
          case "multichoice":
            validanswer = true;
            myarray = [];
            that.$qplayer_question_body.find(`[questionbody${q_id}] [data-element="answer"]`).each(function(e){
              if($(this).prop("checked")){
                myarray.push(this.value);
              }
            });
            if(myarray.length == 0){
              // validanswer = false;
              q_answer = undefined;
              if(question.required == 1){
                validanswer = false; 
                allvalidation.push(validanswer);
              }
            } else {
              q_answer = myarray;
            }
          break;
          default:
          break;
        }
        that.$qplayer_userresponse[q_id] = q_answer;
      });
      console.log("this.$qplayer_userresponse- ", this.$qplayer_userresponse)
      if(!allvalidation.includes(false)){
        this._loadquestion();
        var that = this;
        this._APICall(
          this._prepareRequest(
            "saveSurveyResponce",
            {
              surveyid:that.surveyid,
              eventid:that.eventid,
              userresponse:that.$qplayer_userresponse
            }
          ),
          function (result) {
            that.$form.remove();
            if(result.code == 200){
              displayToast("Sucess", "Survey Submitted Successfully", "success");
            } else {
              displayToast("Error", "Please Try again...", "error");
            }
          }
        );
        that.$apiLoader.removeClass("active");
      } else {
        that.$apiLoader.removeClass("active");
        displayToast("failed","Please answer correctly ", "error");
      }
    },    
    _prepareRequest: function(wsfunction, data) {
      if(this.applang){
        data.lang = this.applang;
      }
      var returndata = {
        "wsfunction":wsfunction,
        "wsargs":data
      }
      if(this.logintoken){
        returndata.wstoken = this.logintoken;
      }
      return JSON.stringify(returndata);
    },
    _APICall: function(requestdata, success) {
      var that = this;
      // console.log("requestdata- ", requestdata)
      // if(this.jCall){
      //     this.jCall.abort();
      // }
      that.$apiLoader.addClass("active");
      this.jCall = $.ajax({
        "url": this.baseURL,
        "method": "POST",
        "timeout": 0,
        "headers": {
          "Content-Type": "application/json"
        },
        "data": requestdata,
        beforeSend:function (){
          // console.log("request beforeSend");
        },
        success: function (data, textStatus, jqXHR) {
          that.$apiLoader.removeClass("active");
          // console.log("data- ", data)
          if(data.code == 100){
            displayToast(data.error.title, data.error.message, "error");
          } else if(data.code != 200){
            that.$apiLoader.removeClass("active");
            displayToast(data.error.title, data.error.message, "error");
          } else {
            success(data);
          }
        }, error: function(){
          // console.log("request error");
          return null;
        }, complete: function(){
          // console.log("request complete");
        }
      });
    }
  }
  showSuccessToast = function() {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: 'Success',
      text: 'And these were just the basic demos! Scroll down to check further details on how to customize the output.',
      showHideTransition: 'slide',
      icon: 'success',
      loaderBg: '#f96868',
      position: 'top-right'
    })
  };
  showInfoToast = function(heading, message) {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: heading,
      text: message,
      showHideTransition: 'slide',
      icon: 'info',
      loaderBg: '#46c35f',
      position: 'top-right'
    })
  };
  showWarningToast = function() {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: 'Warning',
      text: 'And these were just the basic demos! Scroll down to check further details on how to customize the output.',
      showHideTransition: 'slide',
      icon: 'warning',
      loaderBg: '#57c7d4',
      position: 'top-right'
    })
  };
  showDangerToast = function() {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: 'Danger',
      text: 'And these were just the basic demos! Scroll down to check further details on how to customize the output.',
      showHideTransition: 'slide',
      icon: 'error',
      loaderBg: '#f2a654',
      position: 'top-right'
    })
  };
  showToastPosition = function(position) {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: 'Positioning',
      text: 'Specify the custom position object or use one of the predefined ones',
      position: String(position),
      icon: 'info',
      stack: false,
      loaderBg: '#f96868'
    })
  }
  showToastInCustomPosition = function() {
    'use strict';
    resetToastPosition();
    $.toast({
      heading: 'Custom positioning',
      text: 'Specify the custom position object or use one of the predefined ones',
      icon: 'info',
      position: {
        left: 120,
        top: 120
      },
      stack: false,
      loaderBg: '#f96868'
    })
  }
  displayToast = function(heading="", message="", type='info') {
    var toastBgcolors = '#46c35f';
    var toastBgicon = 'info';
    switch(type) {
      case "success": toastBgicon = 'success'; toastBgcolors = '#f96868'; break;
      case "info": toastBgicon = 'info'; toastBgcolors = '#46c35f'; break;
      case "warning": toastBgicon = 'warning'; toastBgcolors = '#57c7d4'; break;
      case "error": toastBgicon = 'error'; toastBgcolors = '#f2a654'; break;
      default: toastBgicon = 'info'; toastBgcolors = '#46c35f'; break;
        // code block
    }
    resetToastPosition();
    $.toast({
      heading: heading,
      text: message,
      showHideTransition: 'slide',
      icon: toastBgicon,
      loaderBg: toastBgcolors,
      position: 'top-right'
    })
  };
  resetToastPosition = function() {
    $('.jq-toast-wrap').removeClass('bottom-left bottom-right top-left top-right mid-center'); // to remove previous position class
    $(".jq-toast-wrap").css({
      "top": "",
      "left": "",
      "bottom": "",
      "right": ""
    }); //to remove previous position style
  }
  plus_prevent_selection = function(e) {
    if(e.target && e.target.offsetParent && /^(stylised-player|stylised-pause|stylised-play|stylised-restart|stylised-time-wrapper)$/i.test(e.target.offsetParent.className)){
      return true;
    }
    console.log("e-", e.target.offsetParent);
    console.log("e-", e.target.offsetParent.className);
    console.log("prevented-plus_prevent_selection");
    return false;
  }
  plus_prevent = function(e) {
    console.log("prevented-plus_prevent");
    e.preventDefault();
  }
  plus_prevent_mouse= function(e) {
    if (/^(span|SPAN|input|INPUT|TEXTAREA|textarea|BUTTON|button|select|SELECT|label|LABEL|a|A|option|OPTION)$/i.test(e.target.nodeName) || /^(span|SPAN|input|INPUT|TEXTAREA|textarea|BUTTON|button|select|SELECT|label|LABEL|a|A|option|OPTION)$/i.test(e.target.localName)) {
        return;
    }
    if(e.target && e.target.offsetParent && /^(stylised-player|stylised-pause|stylised-play|stylised-restart|stylised-time-wrapper)$/i.test(e.target.offsetParent.className)){
      return;
    }
    console.log("e-", e.target.offsetParent);
    console.log("e-", e.target.offsetParent.className);
    console.log("prevented-plus_prevent_mouse");
    e.preventDefault();
  }
  plus_dateToFrench = function(date){
    if(USERLANG == ""){
      USERLANG = "FR";
    }
    if (!date) { return '';}
    date = date;
    months = [];
    days = [];
    days['EN'] = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    days['FR'] = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
    months['EN'] = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    months['FR'] = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    console.log("date- ", date);
    const dateobj = new Date();
    dateobj.setUTCMilliseconds(date);
    console.log("USERLANG- ", USERLANG);
    console.log("dateobj- ", dateobj);
    var d = dateobj.getDate();
    var m = dateobj.getMonth();
    var month = months[USERLANG][m];
    var y = dateobj.getFullYear();
    var h = dateobj.getHours();
    var i = dateobj.getMinutes();
    return `${d} ${month} ${y} ${h}:${i}`;
  }
  getAPIRequest = function(fname, args){
    var settings = {
      "url": CFG.wwwroot+"/app_rest_api/offline/index.php",
      "method": "POST",
      "timeout": 0,
      "headers": {
        "Content-Type": "application/json",
      },
        "data": JSON.stringify({
          "wsfunction": fname,
          "wsargs": args
        }),
    };
    return settings;
  };
  consolelog = function (string, data="") {
    console.log(string, data);
  };
  get_string = function (key, page="") {
    var lang = CFG.lang;
    var LANGUAGESTRINGS = CFG.langstrings;
    if(LANGUAGESTRINGS[lang] != undefined){
      var stringarray = LANGUAGESTRINGS[lang];
      var finalkey = ((page != "")?page+"_":"")+key;
      if(stringarray[finalkey] != undefined){
        return stringarray[finalkey];
      } else {
        return `${lang}[${finalkey}]`;
      }
    } else {
      return `${lang}[${key}]`;
    }
  }
  datadynamicprogress = function(element){
    var syncdata = element.data("args");
    try {
      var exsyncdata = JSON.parse(window.atob(syncdata));
      if(exsyncdata.wsfunction){
        var args = getAPIRequest(exsyncdata.wsfunction, exsyncdata.args);
        $.ajax(args).done(function (response) {
          if(response?.code == 200 && response?.data?.havemore){
            const progressdata = response?.data?.progress;
            if(progressdata){
              element.find(".progress-bar").removeClass("bg-gradient-info");
              element.find(".progress-bar").removeClass("bg-gradient-success");
              element.find(".progress-bar").removeClass("bg-gradient-warning");
              element.find(".progress-bar").removeClass("bg-gradient-primary");
              element.find(".progress-bar").removeClass("bg-gradient-danger");
              element.find(".progress-bar").addClass(getprogressclass(progressdata.percent));
              element.find(".progress-bar").css("width", `${progressdata.percent}%`);
              var message = `${progressdata.percent}%`;
              if(progressdata.message){
                message = `${progressdata.message} (${progressdata.percent}%)`;
              }
              element.find(".progress-bar").html(message);
              setTimeout(function(){datadynamicprogress(element)}, 10000);
            }
          } else {
            element.remove();
          }
        });
      }
    }
    catch(err) {
      document.getElementById("demo").innerHTML = err.message;
    }
  }
  getprogressclass = function(percent=0){
    var colorclass="";
    if(percent >= 80){
        colorclass="bg-gradient-info";
    } else if(percent >= 60){
        colorclass="bg-gradient-success";
    } else if(percent >= 40){
        colorclass="bg-gradient-warning";
    } else if(percent >= 20){
        colorclass="bg-gradient-primary";
    } else {
        colorclass="bg-gradient-danger";
    }
    return colorclass;
  }
})(jQuery);
(function($) {
  'use strict';
  $(function() {
    $(document).delegate("*", "contextmenu", function(e){ plus_prevent(e); });
    $(document).delegate("*", "mousedown", function(e){ plus_prevent_mouse(e); });
    $(document).delegate("*", "mouseup", function(e){ plus_prevent_mouse(e); });
    // $(document).delegate("*", "dragstart", function(e){ plus_prevent(e); });
    // $(document).delegate("*", "selectstart", function(e){ plus_prevent_selection(e); });
    $(document).delegate("*", "cut", function(e){ plus_prevent(e); });
    $(document).delegate("*", "copy", function(e){ plus_prevent(e); });
    $(document).delegate("*", "paste", function(e){ plus_prevent(e); });
    // $(document).delegate("*", "cut", function(e){ plus_prevent(e); });
    // $(window).on("beforeprint", function(){ document.body.hidden = true; });
    // $(window).on("afterprint", function(){ document.body.hidden = false; });
    $('[userchoiceupdate]').click(function() {
      var args = getAPIRequest("userChoiceUpdate", {
        "element": $(this).data("choice")
      });
      $.ajax(args).done(function (response) {});
    });
    $('[tooglesidebar]').click(function() {
      var args = getAPIRequest("userChoiceUpdate", {
        "element": $(this).data("choice"),
        "value": $("body").hasClass("sidebar-icon-only")
      });
      $.ajax(args).done(function (response) {});
    });
    $(document).on("click", '[startsurvey]', function() {
      var container = $(this).data("container");
      var id = $(this).data("id");
      var eventid = $(this).data("eventid");
      console.log("id: ", id),
      console.log("eventid: ", eventid),
      console.log("container: ", container);
      if($(container).length > 0){
        $(container).html(surveyController.prepareSurveyPlayer(id, eventid));
        surveyController.init($, $('[surveyplayer]'));
      }
    });
    $('.plus_local_datatable').each(function() {
      console.log("CFG: ", CFG);
      var ordering = !$(this).hasClass("nosort");
      $(this).DataTable({
        aLengthMenu: [
          [5, 10, 15, -1],
          [5, 10, 15, `${get_string("all", "site")}`]
        ],
        ordering:ordering,
        iDisplayLength: 10,
        language: {
          lengthMenu: `${get_string("showing", "form")} _MENU_ ${get_string("records", "form")}`,
          search: `${get_string("search", "form")}`,
          info: `${get_string("showing", "form")} _PAGE_ ${get_string("of", "form")} _PAGES_`,
          paginate: {
            previous: "Precedent",
            next: "Suivant"
          }
        }
      });
    });
    $('[datadynamicprogress]').each( function() {
      datadynamicprogress($(this));
    });
  });
})(jQuery);