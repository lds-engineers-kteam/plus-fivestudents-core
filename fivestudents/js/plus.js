(function($) {
  var dragSrcEl = null;
  var touchEl = null;
  var lastMove = null;
  function dragStart(e) {
    dragSrcEl = this.cloneNode(false);
  };

  function dragEnter(e) {
    this.classList.add('drag-over');
  }

  function dragLeave(e) {
    e.stopPropagation();
    this.classList.remove('drag-over');
  }

  function dragOver(e) {
    e.preventDefault();
    return false;
  }

  function dragDrop(e) {
      $(this).html("");
    if (dragSrcEl.classList.contains('drag-item--prepend')) {
      this.prepend(dragSrcEl);
      // this.innerHTML(dragSrcEl);
    } else {
      dragSrcEl.innerHTML = ((dragSrcEl.dataset.type==1)?`<img src="${dragSrcEl.dataset.text}" />`:dragSrcEl.dataset.text); 

      this.appendChild(dragSrcEl);
      // this.innerHTML(dragSrcEl);
    }
    return false;
  }

  function dragEnd(e) {
    var listItems = document.querySelectorAll('.drag-container');
    [].forEach.call(listItems, function(item) {
      item.classList.remove('drag-over');
    });
  }

  function touchStart(e) {
    e.preventDefault();
    this.classList.add('drag-item--touchmove');
  }

  var scrollDelay = 0;
  var scrollDirection = 1;
  function pageScroll(a, b) {
    window.scrollBy(0,scrollDirection); // horizontal and vertical scroll increments
    scrollDelay = setTimeout(pageScroll,5); // scrolls every 100 milliseconds

    if (a > window.innerHeight - b) { scrollDirection = 1; }
    if (a < 0 + b) { scrollDirection = -1*scrollDirection; }
  }

  var x = 1;
  function touchMove(e) {
    var touchLocation = e.targetTouches[0],
        w = this.offsetWidth,
        h = this.offsetHeight;

    lastMove = e;
    touchEl = this.cloneNode(false);
    this.style.width = w + 'px';
    this.style.height = h + 'px';
    this.style.position = 'fixed';
    this.style.left = touchLocation.clientX - w/2 + 'px';
    this.style.top = touchLocation.clientY - h/2 + 'px';

    if (touchLocation.clientY > window.innerHeight - h || touchLocation.clientY < 0 + h) {
      if (x === 1) {
        x = 0;
        pageScroll(touchLocation.clientY, h);
      }
    } else {
      clearTimeout(scrollDelay);
      x = 1;
    }
  }

  function touchEnd(e) {
    var box1 = this.getBoundingClientRect(),
        x1 = box1.left,
        y1 = box1.top,
        h1 = this.offsetHeight,
        w1 = this.offsetWidth,
        b1 = y1 + h1,
        r1 = x1 + w1;

    var targets = document.querySelectorAll('.drag-container');
    [].forEach.call(targets, function(target) {
      var box2 = target.getBoundingClientRect(),
          x2 = box2.left,
          y2 = box2.top,
          h2 = target.offsetHeight,
          w2 = target.offsetWidth,
          b2 = y2 + h2,
          r2 = x2 + w2;

      if (b1 < y2 || y1 > b2 || r1 < x2 || x1 > r2) {
        return false;
      } else {
          console.log("touchEl- ", touchEl)
          touchEl.style.position = "relative";
          touchEl.style.left = "unset";
          touchEl.style.top = "unset";
          touchEl.innerHTML = ((touchEl.dataset.type==1)?`<img src="${touchEl.dataset.text}" />`:touchEl.dataset.text); 
          // console.log("target- ", $(target).html("fdsfsdf"))
          $(target).html("");
        if (touchEl.classList.contains('drag-item--prepend')) {
          target.prepend(touchEl);
          // target.html(touchEl);
        } else {
          target.appendChild(touchEl);
          // target.html(touchEl);
        }
      }
    });

    this.removeAttribute('style');
    this.classList.remove('drag-item--touchmove');
    clearTimeout(scrollDelay);
    x = 1;
  }
  tinyPlayer= {
    Player: class {
      constructor(e) {
        (this._playlist = e), (this._index = 0), (this._mouse_down = !1), (this._seeking = !1), (this._animation_timestamp = 0), (this.ns = tinyPlayer);
      }
      play(e) {
        var n = this;
        (e = "number" == typeof e ? e : n._index), n._playlist[e].howl.play(), (n._index = e);
      }
      pause(e) {
        e = "number" == typeof e ? e : this._index;
        console.log("pause e- ", e)
        console.log("pause this- ", this)
        e = this._playlist[e].howl;
        e && e.pause();
      }
      stop(e) {
        var n = this;
        (e = "number" == typeof e ? e : n._index), n._playlist[e].howl.stop(), (n._index = e);
      }
      toggleTo(e) {
        var n = this,
            t = n._playlist[n._index].howl;
            console.log("e- ", e)
            console.log("n- ", n)
        if (t.playing()) {
            if (e === n._index) return void n.pause();
            t.stop();
        }
        n.play(e);
      }
      seek(e, n) {
        var t = this.ns,
          i = this._playlist[e].howl,
          e = this._playlist[e].html_elem,
          n = n * i.duration();
        i.seek(n), e.find(".song-timer").html(t._formatTime(n) + " / " + t._formatTime(i.duration()));
      }
      updateDuration(e, n) {
        this._playlist[e].html_elem.find(".song-progress").css("width", (100 * n || 0) + "%");
      }
      volume(n) {
        Howler.volume(n);
        this._playlist.forEach(function (e) {
            e.html_elem.find(".song-volume-bar#fg").css("width", 60 * n + "%"), e.html_elem.find(".song-volume-dot").css("left", 60 * n + 20 + "%");
        });
      }
      step(e) {
        var n = this,
          t = this.ns,
          i = n._playlist[n._index].howl,
          o = n._playlist[n._index].html_elem,
          s = i.seek() || 0;
        o.find(".song-timer").html(t._formatTime(s) + " / " + t._formatTime(i.duration())), n._seeking || n.updateDuration(n._index, s / i.duration());
        o.find(".song-title");
        i.playing() && requestAnimationFrame(n.step.bind(n));
      }
    },
    createPlayerFromTags: function (element) {
      console.log("createPlayerFromTags");
      var o = this,
        s = [];
      $(element).each(function () {
        let e = "";
        e = $(this).attr("data-title") ? $(this).attr("data-title") : "";
        var n = true;
        console.log("n-", n);

        let t = [];
        t.push($(this).attr("src"));
        var i = o.createSongPlayer(e);
        s.push({ title: e, files: t, html_elem: i, preload: n, howl: null }), $(this).after(i), $(this).hide();
      });
      var e = new o.Player(s);
        return o._setupEvents(e), e;
    },
    createSongPlayer: function (e) {
      return $("<div>")
        .addClass("iru-tiny-player")
        .append($("<div>").addClass("song-seek"))
        .append($("<div>").addClass("song-progress"))
        .append(
          $("<div>")
            .addClass("song-main-info")
            .append($('<div class="icon fa-play">'))
            .append($('<div class="icon fa-pause">').hide())
            .append($('<div class="icon fa-stop">'))
            .append($('<div class="song-title">').html(e + "  "))
            .append($("<div>").addClass("song-timer"))
            .append($('<div class="icon fa-volume-up">'))
        )
        .append(
          $("<div>")
            .addClass("song-volume-control")
            .hide()
            .append($("<div>").addClass("song-volume-bar").attr("id", "bg"))
            .append($("<div>").addClass("song-volume-bar").attr("id", "fg"))
            .append($("<div>").addClass("song-volume-bar").attr("id", "fgg"))
            .append($("<div>").addClass("song-volume-dot"))
            .append($("<div>").addClass("icon fa-times"))
        );
    },
    _setupEvents: function (i) {
      var o = this,
        s = 0;
      i._playlist.forEach(function (e) {
        var n = e.html_elem,
          t = s;
        (e.howl = o._createHowlForPlayer(i, e)), o._bindPlayerControls(i, n, t), ++s;
      });
    },
    _createHowlForPlayer: function (e, n) {
      var t = n.html_elem,
          i = this;
      return new Howl({
        src: n.files,
        html5: !0,
        preload: n.preload,
        onplay: function () {
          t.find(".fa-play").hide(), t.find(".fa-pause").show(), requestAnimationFrame(e.step.bind(e));
        },
        onload: function () {
          t.find(".song-timer").html(i._formatTime(this.duration()));
        },
        onend: function () {
          t.find(".song-timer").html(i._formatTime(this.duration())), t.find(".song-progress").css("width", "0%"), t.find(".song-title").html(n.title + "  "), t.find(".fa-pause").hide(), t.find(".fa-play").show();
        },
        onpause: function () {
          t.find(".fa-pause").hide(), t.find(".fa-play").show();
        },
        onstop: function () {
          t.find(".song-timer").html(i._formatTime(this.duration())), t.find(".song-progress").css("width", "0%"), t.find(".song-title").html(n.title + "  "), t.find(".fa-pause").hide(), t.find(".fa-play").show();
        },
      });
    },
    _bindPlayerControls: function (t, i, n) {
      i.find(".fa-play").click(function () {
          t.toggleTo(n);
      }),
      i.find(".fa-pause").click(function () {
        t.pause(n);
      }),
      i.find(".fa-stop").click(function () {
        t.stop(n);
      }),
      i.find(".fa-volume-up").click(function () {
        i.find(".song-volume-control").show();
      }),
      i.find(".fa-times").click(function () {
        i.find(".song-volume-control").hide();
      }),
      i.find(".song-volume-bar#fgg").click(function (e) {
        var n = (n = e.pageX) - i.find(".song-volume-bar#bg").offset().left - 7.5,
          e = parseFloat(i.find(".song-volume-bar#bg").innerWidth());
        t.volume(n / e);
      }),
      i.find(".song-volume-dot").mousedown(function () {
        t._mouse_down = !0;
      }),
      i.find(".song-volume-control").mouseup(function () {
        t._mouse_down = !1;
      }),
      i.find(".song-volume-control").mousemove(function (e) {
          var n;
        t._mouse_down && ((n = (n = e.pageX) - i.find(".song-volume-bar#bg").offset().left - 7.5), (e = parseFloat(i.find(".song-volume-bar#bg").innerWidth())), (e = Math.min(1, Math.max(0, n / e))), t.volume(e));
      });
      function o(e, n, t) {
        return (e = e.pageX), (e -= n.find(t).offset().left), (n = parseFloat(n.find(".song-seek").innerWidth())), Math.min(1, Math.max(0, e / n));
      }
      i.find(".song-seek").mousedown(function (e) {
        (t._seeking = !0), t.updateDuration(n, o(e, i, ".song-seek"));
      }),
      i.find(".song-seek").mousemove(function (e) {
        t._seeking && t.updateDuration(n, o(e, i, ".song-seek"));
      }),
      i.find(".song-seek").mouseup(function () {
        (t._seeking = !1), t.seek(n, o(event, i, ".song-seek"));
      }),
      i.find(".song-progress").mousedown(function (e) {
        (t._seeking = !0), t.updateDuration(n, o(e, i, ".song-progress"));
      }),
      i.find(".song-progress").mousemove(function (e) {
        t._seeking && t.updateDuration(n, o(e, i, ".song-progress"));
      }),
      i.find(".song-progress").mouseup(function () {
        (t._seeking = !1), t.seek(o(n, event, i));
      });
    },
    _formatTime: function (e) {
      var n = Math.floor(e / 3600) || 0,
        t = Math.floor((e - 3600 * n) / 60) || 0,
        e = Math.floor(e - 60 * t) || 0;
      return 0 < n ? n + ":" + (t < 10 ? "0" : "") + t + ":" + (e < 10 ? "0" : "") + e : t + ":" + (e < 10 ? "0" : "") + e;
    },
  };
  quizController = {
    init: function(t, e) {
      this.jQuery = $ = t,
      this.$form = e,
      this.$apiLoader = e.find("#apiLoader"),
      this.$quizattemptsummary = e.find("[quizattemptsummary]"),
      this.baseURL = "/api/index.php",
      this.quizid = null,
      this.loadedquiz = null,
      this.$startquiz = e.find("[startquiz]"),
      this.$restartquiz = e.find("[restartquiz]"),
      this.$startnextquiz = e.find("[startnextquiz]"),
      this.$qplayer = e.find("[qplayer]"),
      this.$qplayer_header = e.find("[qplayer] [qplayerheader]"),
      this.$qplayer_question_title = e.find("[qplayer] [questiontitle]"),
      this.$qplayer_close = e.find("[qplayer] [qplayerclose]"),
      this.$qplayer_close_popup = e.find("[qplayer] [qplayerclosepopup]"),
      this.$qplayer_question_text = e.find("[qplayer] [questiontext]"),
      this.$qplayer_question_submit = e.find("[qplayer] [questionsubmit]"),
      this.$qplayerrbtnlist = e.find("[qplayerrbtnlist]"),
      this.$qplayer_question_prev = e.find("[qplayer] [prevquestion]"),
      this.$qplayer_question_next = e.find("[qplayer] [nextquestion]"),
      this.$qplayer_question_translation = e.find("[qplayer] [questiontranslation]"),
      this.$qplayer_question_hints = e.find("[qplayer] [questionhints]"),
      this.$qplayer_question_correction = e.find("[qplayer] [questioncorrection]"),
      this.$qplayer_question_current = 0,
      this.$qplayer_question_total = 0,
      this.$qplayer_finished = 0,
      this.$qplayer_pagination_current = e.find("[qplayer] [paginationcurrent]"),
      this.$qplayer_pagination_total = e.find("[qplayer] [paginationtotal]"),
      this.$quizplayerContainer = e.find("[quizplayerContainer]"),
      this.$qplayer_data = null,
      this.$qplayer_type = 0,
      this.jCall = null,
      this.audioplayer = null,
      this.audioplayerurl = null,
      this._initListeners(),
      console.log("quizController init: ", this);
      // console.log("quizController init: ");
    },
    _initListeners: function(){
      var that = this;
      that.quizid = this.$form.data("id"),
      this.$form.on("click", "[startquiz]", function(e) {that._startquiz(this, e)}),
      this.$form.on("click", "[questionsubmit]", function(e) {that._questionsubmit()}),
      this.$form.on("click", "[nextquestion]", function(e) {that._nextquestion(1)}),
      this.$form.on("click", "[prevquestion]", function(e) {that._nextquestion(0)}),
      that._getQuiz();
      console.log("quizController that: ", that);
      // console.log("quizController that: ", this);
    },
    _startquiz: function(element, event) {
      if(this.loadedquiz){

          this.$apiLoader.addClass("active"),
          this.$qplayer_close_popup.removeClass("active"),
          this._startquizplayer(this.loadedquiz.cmid),
          console.log("Quiz to start", this.loadedquiz);
      } else {
          displayToast("Falied", "quiz", "error");
      }
    },
    _startquizplayer: function() {
      var that = this;
      this._APICall(
          this._prepareRequest(
              "getQuizData",
              {
                  moduleid:that.quizid,
              }
          ),
          function (result) {
            if(result.code == 200){
              that.$qplayer_data = result.data,
              that.$qplayer_question_current = 0;
              that.$qplayer_question_total = result.data.questions.length;
              console.log("_startquizplayer that- ", that);
              if(that.$qplayer_question_total > 0){
                that._preparePlayer(),
                that._loadquestion();
              }
            } else {
                displayToast("Error", that._getstring("something_went_wrong"), "error");
            }
          }
      ); 
    },
    _preparePlayer: function() {
      var html = `<div class="qplayer" qplayer>
        <div class="qplayer-header" qplayerheader>
          <div class="qplayer-header-question-title" questiontitle></div>
        </div>
        <div class="qplayer-body">
          <div class="qplayer-body-main">
            <div class="qplayer-body-main-questiontext" questiontext></div>
            <div class="qplayer-body-main-questionbottom">
              <button class="btnquiz qplayer-body-main-questionbottom-btn" prevquestion >Prev</button>
              <button class="btnquiz qplayer-body-main-questionbottom-btn" questionsubmit  data-langplace="text" data-langstring="language_question_player_save">Save Answer</button>
              <button class="btnquiz qplayer-body-main-questionbottom-btn" nextquestion >Next</button>
            </div>
          </div>
        </div>
      </div>`;
      this.$quizattemptsummary.html(html),
      this._bindPlayerElements();
    },
    _bindPlayerElements: function() {
      this.$qplayer = this.$form.find("[qplayer]"),
      this.$qplayer_header = this.$form.find("[qplayer] [qplayerheader]"),
      this.$qplayer_question_title = this.$form.find("[qplayer] [questiontitle]"),
      this.$qplayer_question_text = this.$form.find("[qplayer] [questiontext]"),
      this.$qplayer_question_submit = this.$form.find("[qplayer] [questionsubmit]");
      this.$qplayer_question_prev = this.$form.find("[qplayer] [prevquestion]");
      this.$qplayer_question_next = this.$form.find("[qplayer] [nextquestion]");
    },
    _loadquestion: function() {
      this.$apiLoader.removeClass("active");
      var currentquestion = this.$qplayer_data.questions[this.$qplayer_question_current];
      if(currentquestion){
          var questionDataResponse=this._prepareQuestion(currentquestion, false);
          console.log("questionDataResponse- ", questionDataResponse);
          this.$qplayer_question_title.html(currentquestion.questionTitle),
          this.$qplayer_question_text.html(questionDataResponse),
          this.$qplayer_question_text.find("img[src^='https://fivestudents.s3']").addClass("latex-s3"),
          this._mapQuestion(currentquestion),
          // this.$qplayer_pagination_current.text(this.$qplayer_question_current+1),
          // this.$qplayer_pagination_total.text(this.$qplayer_question_total),
          // // this.$qplayer_question_prev.removeClass("active"),
          // // this.$qplayer_question_next.removeClass("active"),
          console.log("currentquestion- ");
      } else {
          displayToast("Error", "Unable to find question", "error");
      }
    },
    _prepareQuestion: function(questions, onlypreview = false) {
      var that=this;
      let questionTemplate='';
      questionTemplate+=`${questions.questionText}`;
      var questionstatus = "";
      console.log("questions- ", questions);
      switch(questions.type) {
        case "multianswer":
          questions.subQuestion.forEach(function(item,index){
            switch(item.type){
              case "shortanswer":
                var answerstatus="";
                var readonly="";
                if(item.isAttempted){
                  readonly="disabled";
                  const found = item.options.find(element => element.answer == item.userResponse && parseFloat(element.fraction) > 0);
                 
                  if(found){
                    if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                    answerstatus="correct";
                  } else {
                    if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                    answerstatus="incorrect";
                  }
                }
                questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,`<input class="${answerstatus}" ${readonly} type="text" name="answer${item.id}" data-elementkey="${item.key}" data-element="answer" data-elementtye="shortanswer" value="${item.userResponse}" />`);
                break;
              case "dropdown" :
                var optionclass="";
                var readonly="";
                let options=``;
                item.options.forEach(function(option){
                  var selectedoption = "";
                  if(item.isAttempted){
                    readonly="disabled";
                    if(item.userResponse == option.value){
                      selectedoption = " selected ";
                      if(parseFloat(option.fraction) > 0){
                        optionclass = "correct";
                        if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                      } else {
                        if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                        optionclass = "incorrect";
                      }
                    }
                  }
                  options+=`<option ${selectedoption} value="${option.value}">${option.answer}</option>`;
                });
                options+='</select>';
                options=`<select class="${optionclass}" ${readonly} data-elementkey="${item.key}"  name="answer${item.id}" data-element="answer" data-elementtye="dropdown">${options}`;
                questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,options);
                break;
              case "multichoiceh": 
                let checkboxs='';  
                item.options.forEach(function(checkbox){
                    var optionclass="";
                  var readonly="";
                  if(item.isAttempted){
                    readonly="disabled";
                    if(item.userResponse == checkbox.value){
                      if(parseFloat(checkbox.fraction) > 0){
                          if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                          optionclass='correct';
                      } else {
                          if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                          optionclass='incorrect';
                      }
                    }
                  }
                  checkboxs+=`
                  <label style="display: inline-block;padding-right:10px;">
                      <input class="${optionclass}" ${readonly} data-elementkey="${item.key}" type="radio"  name="answer${item.id}"  data-element="answer" data-elementtye="multiselect" value="${checkbox.value}"> ${checkbox.answer}
                  </label>
                    `;
                });
                questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,checkboxs);
                break;
              case "multichoicev":
                let checkboxs1='';  
                item.options.forEach(function(multichoicev){
                  var optionclass="";
                  var readonly="";
                  if(item.isAttempted){
                      readonly="disabled";
                      if(item.userResponse == multichoicev.value){
                        if(parseFloat(multichoicev.fraction) > 0){
                          if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                          optionclass='correct';
                        } else {
                          if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                          optionclass='incorrect';
                        }
                      }
                  }
                  checkboxs1+=`
                    <label style="display: block;padding-right:10px;">
                      <input class="${optionclass}" ${readonly} data-elementkey="${item.key}" type="radio"  name="answer${item.id}"  data-element="answer" data-elementtye="multiselect" value="${multichoicev.value}"> ${multichoicev.answer}
                    </label>
                  `;
                });
                questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,checkboxs1);
                break;
              case "numerical":
                var answerstatus="";
                var readonly="";
                if(item.isAttempted){
                  readonly="disabled";
                  const found = item.options.find(element => element.answer == item.userResponse && parseFloat(element.fraction) > 0);
                  if(found){
                    if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                    answerstatus="correct";
                  } else {
                    if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                    answerstatus="incorrect";
                  }
                }
                questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,`<input class="${answerstatus}" ${readonly} type="number" name="answer${item.id}" data-elementkey="${item.key}" data-element="answer" data-elementtye="numerical" value="${item.userResponse}" />`);
                break;
              }
          });
          break;
        case "multiselect":
          let checkboxs='';
          questions.options.forEach(function(checkbox){
            var optionclass="";
            var readonly="";
            if(questions.isAttempted){
              readonly="disabled";
              var allselected = JSON.parse(questions.userResponse);
              if(allselected.includes(checkbox.value+"")){
                readonly +=" checked";
                if(parseFloat(checkbox.fraction) > 0){
                  if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                  optionclass='correct';
                } else {
                  if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                  optionclass='incorrect';
                }
              } else if(parseFloat(checkbox.fraction)>0){
                if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
              }
            }
            checkboxs+=`<div>
            <label>
                <input type="checkbox" class="${optionclass}" ${readonly} name="multiselect${questions.id}[]"  data-element="answer" data-elementtye="multiselect" value="${checkbox.value}"> ${checkbox.answer}
            </label>
            </div>`;
          });
          questionTemplate+=checkboxs;
          break;
        case "multichoice":
          let radioboxs='';
          let optionclass='';
          questions.options.forEach(function(radiobox){
            var optionclass="";
            var readonly="";
            if(questions.isAttempted){
              readonly="disabled";
              if(questions.userResponse == radiobox.value){
                readonly +=" checked";
                if(parseFloat(radiobox.fraction) > 0){
                  if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                  optionclass='correct';
                } else {
                  if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                  optionclass='incorrect';
                }
              }
            }
            radioboxs+=`<div class="multi_choice">
            <label>
                <input type="radio" class="${optionclass}" ${readonly} name="multichoice${questions.id}"  data-element="answer" data-elementtye="multichoice" value="${radiobox.value}"> ${radiobox.answer}
            </label>
            </div>`;
          });
          questionTemplate+=radioboxs;
          break;
        case "truefalse":
          var truefalseoptions = ``;
          let tf_readonly='';
          var tf_optionclass='';
          questions.options.forEach(function(option){
            var selectedoption = "";
            if(questions.isAttempted){
              tf_readonly="disabled";
              if(questions.userResponse == option.value){
                selectedoption = "selected";
                if(parseFloat(option.fraction) > 0){
                  if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                  tf_optionclass='correct';
                } else {
                  if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                  tf_optionclass='incorrect';
                }
              }
            }                            
            truefalseoptions+=`<option ${selectedoption} value="${option.value}">${option.answer}</option>`;
          });
          let truefalse=`<select class="${tf_optionclass}" name="options${questions.id}" ${tf_readonly} data-element="answer" data-elementtye="truefalse">${truefalseoptions}</select>`
           questionTemplate+= truefalse;
          break;
        case "match":
          let match_text='<table class="match_question">';
          questions.stemOrder.forEach(function(matchitem){
            var optionclass="";
            var readonly="";
            if(questions.isAttempted){
              readonly="disabled";
              const found = questions.choiceOrder.find(element => element.value == matchitem.userResponse);
              if(found){
                if(found.answerText == matchitem.answerText){
                  if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                  optionclass = "correct";
                } else {
                  if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                  optionclass = "incorrect";
                }
              }
            }
            match_text+=`<tr matchitemrow>`;
            match_text+=`<td><input name="txt_match_question_key${questions.id}" class="txt_match_question_key" type="hidden" value="${matchitem.key}">${matchitem.questionText}</td>`;
            match_text+=`<td>`;
            match_text+=`<select class="${optionclass}" ${readonly} name="matchchoice${matchitem.id}" data-element="answer" data-elementtye="match">`;
            questions.choiceOrder.forEach(function(choiceOrder){
              var selectedoption = "";
              if(matchitem.userResponse == choiceOrder.value){
                selectedoption = " selected ";
              }
              match_text+=`<option ${selectedoption} value="${choiceOrder.value}">${choiceOrder.answerText}</option>`;
            });
            match_text+=`</select>`;
            match_text+=`</td>`;
            match_text+=`</tr>`;
          });
          match_text+=`<table>`;//match_text
          questionTemplate+=match_text;
          break; 
        case "shortanswer":
          var answerstatus="";
          var readonly="";
          if(questions.isAttempted){
            readonly="disabled";
            if(questions.rightAnswer == questions.userResponse){
              if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
              answerstatus="correct";
            } else {
              if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
              answerstatus="incorrect";
            }
          }
          let shortanswers=`<div><label >Answer: </label><input class="${answerstatus}" ${readonly} type="text" name="answer${questions.id}" data-element="answer" data-elementtye="shortanswer" value="${questions.userResponse}"></div>`;
          questionTemplate+=shortanswers;
          break;
        case "numerical":
          if(questions.isAttempted){
            readonly="disabled";
            if(questions.rightAnswer == questions.userResponse){
              if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
              answerstatus="correct";
            } else {
              if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
              answerstatus="incorrect";
            }
          }
          let numerical=`<div><label >Answer: </label><input class="${answerstatus}" type="number" name="answer${questions.id}" data-element="answer" data-elementtye="numerical" value="${questions.userResponse}"></div>`;
          questionTemplate+=numerical;
          break;
        case "ddwtos":
          let ddwtos_temp=`<div class="dragableoptions">`;
          var userResponse = [];
          if(questions.userResponse){
            userResponse = JSON.parse(questions.userResponse);
          }
          questions.options.forEach(function(dditem,index){
            ddwtos_temp+=`<span class="drag-item" draggable="true" data-value="${dditem.value}" data-text="${dditem.answer}">${dditem.answer}</span>`;
          });
          ddwtos_temp+=`</div>`;
          var dragtocontaioner = "dragtocontaioner";
          if(questions.isAttempted || onlypreview){
            ddwtos_temp = "";
            dragtocontaioner = "";
          }
          const regexp = /\[[[1-9].]]/g;
          const allelements = [...questionTemplate.matchAll(regexp)];
          allelements.forEach(function(ddposition,dindex){
            var answer = dindex+1;
            var cindex = ddposition[0].replace(/\[/g, "");
            cindex = cindex.replace(/\]/g, "");
            // console.log('dindex- ',dindex);
            // console.log('cindex- ',cindex);
            var selectedanswer = "";
            var selectedanswerclass = "";
            if(questions.isAttempted){
              var selected = userResponse.find(element => element.key == `p${answer}`);
              if(selected){
                var selectedoption = questions.options.find(element => element.value == selected.value);
                if(selectedoption){
                  selectedanswer = selectedoption.answer;
                  if(selectedoption.order == cindex){
                    if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                    selectedanswerclass="correct";
                  } else {
                    if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                    selectedanswerclass="incorrect";
                  }
                } 
              }
            }
            questionTemplate=questionTemplate.replace(ddposition[0],`<span class="drag-container ${selectedanswerclass}" data-elementkey="p${answer}" data-element="answer">${selectedanswer}</span>`);
          });
          questionTemplate=ddwtos_temp+`<div class="${dragtocontaioner}">`+questionTemplate+`</div>`;
          break;
        case "ddimageortext":
          let ddimage_temp=`<div class="dragableoptions">`;
          questions.allDrags.forEach(function(dditem,index){
            ddimage_temp+=`<div class="drag-item" draggable="true" data-value="${dditem.value}" data-type="${(dditem.file?1:0)}" data-text="${(dditem.file?dditem.file.fileurl:dditem.label)}">${(dditem.file?`<img src="${dditem.file.fileurl}" width="25px"/>`:dditem.label)}</div>`;
          });
          ddimage_temp+=`</div>`;
          var alldrops="";
          questionstatus=="";
          questions.allDrops.forEach(function(dditem,index){
            var answer = index+1;
            var selectedanswer = " &nbsp; ";
            var selectedanswerclass = "";
            if(questions.isAttempted){
              var selectedoption = questions.allDrags.find(element => element.value == dditem.userResponse);
              if(selectedoption){
                selectedanswer = `${(selectedoption.file?`<img src="${selectedoption.file.fileurl}" width="25px"/>`:selectedoption.label)}`;
                if(selectedoption.no == dditem.choice){
                  if(questionstatus =="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                  selectedanswerclass="correct";
                } else {
                  if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                  selectedanswerclass="incorrect";
                }
              } 
            }
            alldrops+=`<span class="imagedrops drag-container ${selectedanswerclass}" drag-container" data-elementkey="p${answer}" data-element="answer" style="top:${dditem.yTop*1.5}px; left:${dditem.xLeft*1.5}px;" >${selectedanswer}</span>`;
          });
          var dragtocontaioner = "dragtocontaioner";
          if(questions.isAttempted || onlypreview){
            ddimage_temp = "";
            dragtocontaioner = "";
          }
          questionTemplate +=`<div class="ddinimage ${(questions.isAttempted?'attempted':'')}"><img src="${questions.backgroundImage}" class="ddcontainer"/>${alldrops}</div>`;
          questionTemplate=ddimage_temp+`<div class="${dragtocontaioner}">`+questionTemplate+`</div>`;
          break;
        default:
      }

      if(this.$qplayer_question_current >0){
          that.$qplayer_question_prev.show();
      } else {
          that.$qplayer_question_prev.hide();
      }
      this.$qplayer_question_next.hide();
      if(questions.isAttempted){
        var questionheader = `
          <div class="questiontextheader">
            <div class="questiontextheader-qno-score">
              <div class="questiontextheader-qno">${this._getstring("language_question_player_question")} ${this.$qplayer_question_current+1}/${this.$qplayer_question_total} ${this._getstring("language_eventscreen_note")}</div>
              <div class="questiontextheader-score">${questions.marks} / ${questions.maxMarks}</div>
            </div>
            <div class="questiontextheader-title">${this._getstring("language_question_player_question_"+questionstatus)}</title>
          </div>
        `;
        questionTemplate = questionheader + questionTemplate;
        this.$qplayer_question_next.show();
        this.$qplayer_question_submit.attr("data-terminate", 0);
        if(this.$qplayer_question_current+1 == this.$qplayer_question_total){
          this.$qplayer_question_submit.show();
          this.$qplayer_question_submit.attr("data-terminate", 1);
          this.$qplayer_question_submit.text("Terminate");
          if(!this.$qplayer_finished){
            this._APICall(
              this._prepareRequest(
                "finishAttempt",
                {
                  finishattempt:1,
                  timeup: 0,
                  attemptid: that.$qplayer_data.current.id
                }
              ),
              function (result) {
                that.$qplayer_finished = 1;
              }
            );
          }
        } else {
          this.$qplayer_question_submit.hide();
          this.$qplayer_question_submit.text("Save");
        }
      } else {
        this.$qplayer_question_submit.attr("data-terminate", 0);
        this.$qplayer_question_submit.show()
      }
      if((this.$qplayer_question_current+1) == this.$qplayer_question_total){
        this.$qplayer_question_next.hide();
      }
      return questionTemplate;
    },
    _mapQuestion: function(questions){
      var that = this;
      if(questions.type == "ddwtos" || questions.type == "ddimageortext"){
        var targets = document.querySelectorAll('.drag-container');
        [].forEach.call(targets, function(target) {
          that.drag_addTargetEvents(target);
        });
        var listItems = document.querySelectorAll('.drag-item');
        [].forEach.call(listItems, function(item) {
          that.drag_addEventsDragAndDrop(item);
        });
      }
    },
    drag_addTargetEvents: function(target) {
      target.addEventListener('dragover', dragOver, false),
      target.addEventListener('dragenter', dragEnter, false),
      target.addEventListener('dragleave', dragLeave, false),
      target.addEventListener('drop', dragDrop, false);
    },
    drag_addEventsDragAndDrop: function(el) {
      el.addEventListener('dragstart', dragStart, false),
      el.addEventListener('dragend', dragEnd, false),
      el.addEventListener('touchstart', touchStart, false),
      el.addEventListener('touchmove', touchMove, false),
      el.addEventListener('touchend', touchEnd, false);
    },
    _questionsubmit: function() {
      this.stopAudioPlaying();
      var that=this;
      that.$apiLoader.addClass("active");
      //added by dk
      var currentquestion = this.$qplayer_data.questions[this.$qplayer_question_current];
      var q_id = currentquestion.id;
      var q_text = currentquestion.questionText;
      var q_type =   currentquestion.type;
      var q_lang = '';
      var q_answer='';
      var myarray = [];
      var ans_data;
      var validanswer = true;
      console.log("Quesstion type : "+q_type);
      if(!currentquestion.isAttempted){
        switch (q_type) {
          case "multichoice":
            this.$qplayer_question_text.find('[data-element="answer"]').each(function(e){
              if($(this).prop("checked")){
                q_answer = this.value;
                if(!q_answer){validanswer = false; }
              }
            });
            break;
          case "multiselect":
            validanswer = true;
            myarray = [];
            this.$qplayer_question_text.find('[data-element="answer"]').each(function(e){
                if($(this).prop("checked")){
                    myarray.push(this.value);
                }
            });
            if(myarray.length == 0){
                validanswer = false;
            }
            q_answer = myarray;
            break;
          case "match":
            validanswer = true;
            this.$qplayer_question_text.find('[matchitemrow]').each(function(item){                            
              let key, value;
              key=that.jQuery(this).find('.txt_match_question_key').val();
              value= that.jQuery(this).find('[data-element="answer"]').val();
              myarray.push({key, value});                     
              if(!value){validanswer = false; }
            });
            q_answer = myarray;
            break;
          case "truefalse":
          case "shortanswer":
            q_answer = this.$qplayer_question_text.find('[data-element="answer"]').val();
              if(!q_answer){validanswer = false; }
            break;
          case "ddwtos":
            validanswer = true;
            this.$qplayer_question_text.find('[data-element="answer"]').each(function(e){
              let key, value;
              key=that.jQuery(this).attr('data-elementkey');
              value= that.jQuery(this).find(".drag-item").data("value");
              if(!value){validanswer = false; }
              myarray.push({key, value}); 
            });
            q_answer = myarray;
            break;
          case "ddimageortext":
            validanswer = true;
            this.$qplayer_question_text.find('[data-element="answer"]').each(function(e){
              let key, value;
              key=that.jQuery(this).attr('data-elementkey');
              value= that.jQuery(this).find(".drag-item").data("value");
              if(!value){validanswer = false; }
              myarray.push({key, value}); 
            });
            q_answer = myarray;
            break;
          case "multianswer":
            validanswer = true;
            var allanswer = {};
            this.$qplayer_question_text.find('[data-element="answer"]').each(function(item){
              let key, value;
              key=that.jQuery(this).attr('data-elementkey');
              value= that.jQuery(this).val();
              if(allanswer[key] === undefined){
                  allanswer[key] = "";
              }
              if(that.jQuery(this).attr("type")=="radio"){
                  if(that.jQuery(this).prop("checked")){
                      allanswer[key] = value;
                  }
              } else if(that.jQuery(this).data("elementtye") == "dropdown") {
                  allanswer[key] = value;
                  if(value < 0){validanswer = false; }
              } else {
                  allanswer[key] = value;
              }
              // console.log("value- ",value)
              if(!value){validanswer = false; }
            });
            for (const [key, value] of Object.entries(allanswer)) {
              myarray.push({key, value}); 
            }
            q_answer = myarray;
            break;
          default:
            break;
        }
        if(validanswer){
          // console.log(q_id+" "+'q_answer',q_answer);
          this._APICall(
            this._prepareRequest(
              "saveAnswer",
              {
                wsquestionatmpid:q_id,
                wsanswer_data: q_answer
              }
            ),
            function (result) {
              // console.log("result- ", result);
              if(result.data.question){
                that.$qplayer_data.questions[that.$qplayer_question_current] = result.data.question;
                if(that.$qplayer_question_current+1 < that.$qplayer_question_total){
                  // this.$qplayer_question_next.addClass("active");
                  that._loadquestion();
                  displayToast("Success","Saved successfully", "success");
                } else {
                  that._loadquestion();
                  displayToast("Success","Need to submit quiz", "warning");
                }
              } else {
                that.$apiLoader.removeClass("active");
                displayToast("failed","API re=sponse error ", "error");
              }
            }
          );
        } else {
          that.$apiLoader.removeClass("active");
          displayToast("failed","Please answer correctly ", "error");
        }
      } else {
        that.$apiLoader.removeClass("active");
        displayToast("Success","submitted successfully", "success");
        that.$apiLoader.addClass("active"),
        that._getQuiz();
      }
    },
    stopAudioPlaying: function() {
      if(this.audioplayer){
        this.audioplayer.pause();
        this.jQuery("[toggleplay]").removeClass("active");
      }
    },
    _getQuiz: function() {
      var that = this;
      this._APICall(
        this._prepareRequest(
          "getQuizStatus",
          {
            moduleid:that.quizid
          }
        ),
        function (result) {
          if(result.code == 200){
            that.loadedquiz = result.data,
            that._loadsummary();
          } else {
              displayToast("Error", "Please Try again...", "error");
          }
        }
      );
    },
    _loadsummary: function() {
      console.log("quiz===", this.loadedquiz)
      if(this.loadedquiz){
        var htmldata = ``;
        htmldata += `<h2 class="quizname">${this.loadedquiz.name}</h2>`;
        htmldata += `<div class="quizintro">${this.loadedquiz.intro}</div>`;
        var newattempt = true;
        if(Array.isArray(this.loadedquiz.attempts) && this.loadedquiz.attempts.length > 0){
          htmldata += `<div class="table-responsive"><table class="table table-stripped"><thead><tr><th>Attempt</th><th>State</th><th>Score</th></tr></thead><tbody>`;
          this.loadedquiz.attempts.forEach(function(attempt){
            var score ='';
            if(attempt.state == "finished"){
              score =attempt.sumGrades;
            } else {
              newattempt = false;
            }
            htmldata += `<tr><td>${attempt.attempt}</td><td>${attempt.state}</td><td>${score}</td></tr>`;
          });
          htmldata += `</tbody></table></div>`;

        }
        htmldata += `<div class="quizbtns">`;
        if(newattempt){
          htmldata += `<button class="btn btn-primary" startquiz >Attempt Now</h2>`;
        } else {
          htmldata += `<button class="btn btn-primary" startquiz>Continue the last attempt</h2>`;
        }
        htmldata += `</div>`;
        htmldata += ``;
        this.$quizattemptsummary.html(htmldata);
      } else{
        displayToast("Error", "Failed to load quiz", "error");
      }
    },
    _nextquestion: function(isnext) {
      console.log("isnext- ", isnext);
      this.stopAudioPlaying();
      if(isnext){
        if(this.$qplayer_question_current+1 < this.$qplayer_question_total){
          this.$qplayer_question_current = this.$qplayer_question_current+1;
          this._loadquestion();
        } else {
          this._needtosubmitquiz();
        }
      } else {
        this.$qplayer_question_current = this.$qplayer_question_current-1;
        this._loadquestion();
      }
    },
    _needtosubmitquiz: function() {
      alert("neet to submit quiz");
    },
    prepareQuizPlayer: function(quiz) {
      console.log('quiz------',quiz);
      return `<div class="quizmodule" quizplayer data-id="${quiz.id}">
                <div class="quizsummary" quizattemptsummary >
                </div>
                <div id="apiLoader" class="apiLoader active">
                  <img src="/wp-content/plugins/el-dashboard/public/images/ajax-loader-white.gif"/>
                </div>
              </div>`;
    },
    _getcurrentlanguage: function() {
      this.applang = localStorage.getItem('applang');
      if(this.applang){
          return this.applang;
      } else {
          return "fr";
      }
    },
    _getstring: function(key) {
      var lang = this._getcurrentlanguage();
      if(langdata[lang] && langdata[lang][key]){
          return langdata[lang][key];
      } else if(langdata['fr'][key]) {
          return langdata['fr'][key];
      } else {
          return `[${key}]`;
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
  surveyController = {
    init: function(t, e) {
      this.jQuery = $ = t,
      this.$form = e,
      this.$apiLoader = e.find("#apiLoader"),
      this.baseURL = "/api/index.php",
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
      return `<div class="surveymodule quizmodule" surveyplayer data-id="${surveyid}" data-eventid="${eventid}">
                <div class="splayer" qplayer>
                  <div class="splayer-header" qplayerheader>
                  </div>
                  <div class="splayer-body">
                    <div class="splayer-body-main">
                      <div questioncontainer>
                        <div questionbody>
                          <div class="splayer-header-question-title" questiontitle></div>
                          <div class="splayer-body-main-questiontext" questiontext></div>
                        </div>
                      </div>
                      <div class="splayer-body-main-questionbottom">
                        <button class="btnquiz splayer-body-main-questionbottom-btn" style="display:none;" questionsubmit  data-langplace="text" data-langstring="language_question_player_save">Save Answer</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="apiLoader" class="apiLoader active">
                  <img src="/wp-content/plugins/el-dashboard/public/images/ajax-loader-white.gif"/>
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
  generateLinksetting = function($link){
    var settings = {
      "url": "https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyB6QKpOJe_qNbiMnx4aQw4zK10dzUvueNM",
      "method": "POST",
      "timeout": 0,
      "headers": {
        "Content-Type": "application/json"
      },
      "data": JSON.stringify({
        "longDynamicLink": "https://fivestudents.page.link/?link=https://www.fivestudents.com/joinGroup?"+$link
      }),
    };
    return settings;
  }
  getAPIRequest = function(fname, args){
    var settings = {
      "url": "/api/index.php",
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
  }
  imageexportData = function(selector){
    var reqargs = {
        "content": $("#"+selector).html()
    };
    var shortlinksetting = getAPIRequest("printHtmlToImage",reqargs);
    $.ajax(shortlinksetting).done(function (response) {
      console.log("response- ", response)
      if(response.data && response.data){
        const link = document.createElement("a");
        link.href = response.data;
        link.download = "filename.jpg";
        link.target = "_blank";
        link.click();
      } else {
        alert("Failed, try again");
      }
    });
  }
  htmltopdfexport = function(selector, title){
     var divContents = $("#"+selector).html();
    var printWindow = window.open('', '', 'height=400,width=800');
    printWindow.document.write('<html><head><title>'+title+'</title>');
    printWindow.document.write("<link rel='stylesheet' href='https://qaplus.fivestudents.com/api/print.css' type='text/css' media='all' />");
    printWindow.document.write('</head><body ><div class="noprint" style="text-align:center;"><button onclick = "window.print(),window.close();"> Print </button></div>');
    printWindow.document.write(divContents);
    printWindow.document.write('<div class="noprint" style="text-align:center;"><button onclick = "window.print(),window.close();"> Print </button></div></body></html>');
    printWindow.document.close();
    // printWindow.print();
  }
  exportData = function(tableid){
    /* Get the HTML data using Element by Id */
    var table = document.getElementById(tableid);
 
    /* Declaring array variable */
    var rows =[];
 
      //iterate through rows of table
    for(var i=0,row; row = table.rows[i];i++){
        //rows would be accessed using the "row" variable assigned in the for loop
        //Get each cell value/column from the row
        var column = [];
        for(var j=0; j < row.cells.length;j++){
          column.push(row.cells[j].innerText);
        }
 
    /* add a new records in the array */
        rows.push(
            column
        );
 
        }
        csvContent = "data:text/csv;charset=utf-8,";
         /* add the column delimiter as comma(,) and each row splitted by new line character (\n) */
        rows.forEach(function(rowArray){
            row = rowArray.join(",");
            csvContent += row + "\r\n";
        });
 
        /* create a hidden <a> DOM node and set its download attribute */
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", tableid+".csv");
        document.body.appendChild(link);
         /* download the data file named "Stock_Price_Report.csv" */
        link.click();
  }
  plus_prevent_selection = function(e) {
    if(e.target && e.target.offsetParent && /^(stylised-player|stylised-pause|stylised-play|stylised-restart|stylised-time-wrapper)$/i.test(e.target.offsetParent.className)){
      return true;
    }
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
    e.preventDefault();
  }
  loadResourceDetails=function(){
    console.log("tinyPlayer: ", tinyPlayer);
    var reqargs = {
        "id": $("#activitydetails").data("id"),
        "page": $("#activitydetails").data("page"),
        "resource": $("#activitydetails").data("resource"),
    };
    $("#activitydetails").find(".plusplayer").remove();
    var APIREQ = getAPIRequest("getResourceDetails",reqargs);
    APIREQ.timeout=30000;
    $(".pageloading").addClass("active");
    var breadcrumbs="";
    $.ajax(APIREQ).done(function (response) {
      $(".pageloading").removeClass("active");
      if(response.data){
        $("#activity_name").html(response.data.name);
        if(Array.isArray(response.data.breadcrumbs)){
          breadcrumbs+= `<ol class="breadcrumb">`;
          for (var i = 0; i < response.data.breadcrumbs.length; i++) {
            var belement = response.data.breadcrumbs[i];
            breadcrumbs+= `<li class="breadcrumb-item"><a href="/resources?t=${belement.id}">${belement.name}</a></li>`;
          }
          breadcrumbs+= `</ol>`;
        }
        $("#resource_breadcrumb").html(breadcrumbs);
        console.log(`response:-----`, response.data);
        if(response.data.mod == "resource"){
          if( response.data.filetype =='image'){
            $("#activitydetails").html('<img src="'+response.data.url+'" alt="IMG" width="100%" height="auto">');
          }else if( response.data.filetype =='video'){
            $("#activitydetails").html('<div class="plusplayer"><span class="customvideo" src="'+response.data.url+'" data-title="'+response.data.filename+'" id="'+response.data.id+'"></span></div>');
            $('.customvideo').stylise();
          }else if( response.data.filetype =='audio'){
            $("#activitydetails").html('<div class="plusplayer"><span class="customaudio" src="'+response.data.url+'" data-title="'+response.data.filename+'" id="'+response.data.id+'"></span></div>');
            // tinyPlayer.createPlayerFromTags(".resourceplayer");
            $('.customaudio').stylise();
          }else if( response.data.filetype =='pdf'){
            $("#activitydetails").html('<img src="'+response.data.url+'" alt="IMG" width="100%" height="auto"> <div class="btn-parent"><button class="preresporce btn btn-primary">Previous</button> <button class="nextresporce btn btn-primary">Next</button></div>');
            // console.log('ttpage--',response.data.totalpage);
            if((response.data.totalpage -1 ) == response.data.currentres){
              $(".nextresporce").attr("disabled", true);
            }
            if(response.data.currentres == 0){
              $(".preresporce").attr("disabled", true);
            }
          }else{
            displayToast("Message", `${response.data.mod} is Still in Development for file type ${response.data.filetype}`, "info");
          }
        } else if(response.data.mod == "quiz"){
            console.log('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
            $("#activitydetails").html(quizController.prepareQuizPlayer(response.data));
            quizController.init($, $("[quizplayer]"));
            // quizController.init($, $("[qplayer]"));

        } else {
          displayToast("Message", response.data.mod+" Still in Development", "info");
        }
      } else {
        displayToast("Error","Please Try Again", "error");
      }
    }).fail(function(error){
      $(".pageloading").removeClass("active");
      displayToast("Error","Please Try Again", "error");
    });
  }
  updateResourceDetails=function(){
    console.log("tinyPlayer: ", tinyPlayer);
    var reqargs = {
        "id": $("#activitydetails").data("id"),
        "page": $("#activitydetails").data("page"),
        "resource": $("#activitydetails").data("resource"),
    };
    var APIREQ = getAPIRequest("getResourcepdfurl",reqargs);
    $(".pageloading").addClass("active");
    var breadcrumbs="";
    $.ajax(APIREQ).done(function (response) {
      $(".pageloading").removeClass("active");
      if(response.data){
        if(response.data.mod == "resource" && response.data.filetype =='pdf'){
          $("#activitydetails").html('<img src="'+response.data.url+'" alt="IMG" width="100%" height="auto"> <div class="btn-parent"><button class="preresporce btn btn-primary">Previous</button> <button class="nextresporce btn btn-primary">Next</button></div>');
          if((response.data.totalpage -1 ) == response.data.currentres){
            $(".nextresporce").attr("disabled", true);
          }
          if(response.data.currentres == 0){
            $(".preresporce").attr("disabled", true);
          }
        }else {
          displayToast("Message", "Invalid Request", "info");
        }
      } else {
        displayToast("Error","Please Try Again", "error");
      }
    });
  }
  loadNextResourceDetails = function(){
    var page = $("#activitydetails").data("page");
    $("#activitydetails").data("page", page+1);
    updateResourceDetails();
  }
  loadpreResourceDetails = function(){
    var page = $("#activitydetails").data("page");
    $("#activitydetails").data("page", page-1);
    updateResourceDetails();
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

})(jQuery);
(function($) {
  'use strict';
  $(function() {
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
      var ordering = !$(this).hasClass("nosort");
      $(this).DataTable({
        aLengthMenu: [
          [5, 10, 15, -1],
          [5, 10, 15, "Tous"]
        ],
        ordering:ordering,
        iDisplayLength: 10,
        language: {
          lengthMenu: "Afficher _MENU_ enregistrements",
          search: "Rechercher",
          paginate: {
            previous: "Precedent",
            next: "Suivant"
          }
        }
      });
    });
    $('.plus_local_datatable').each(function() {
      var datatable = $(this);
      // SEARCH - Add the placeholder for Search and Turn this into in-line form control
      var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
      // search_input.attr('placeholder', 'Rechercher');
      search_input.removeClass('form-control-sm');
      // LENGTH - Inline-Form control
      var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
      length_sel.removeClass('form-control-sm');
      // var previousbtn = datatable.closest('.dataTables_wrapper').find('li.paginate_button.previous>a');
      // previousbtn.html("Precedent");
      // var nextbtn = datatable.closest('.dataTables_wrapper').find('li.paginate_button.next>a');
      // nextbtn.html("Suivant");
      var tableinfo = datatable.closest('.dataTables_wrapper').find('.dataTables_info');
      var tableinfo_text = tableinfo.text();
      var f = ['Showing','to','of', 'entries'];
      var r = ['Afficher','a','de', 'enregistrements'];
      $.each(f,function(i,v) {
          tableinfo_text = tableinfo_text.replace(new RegExp('\\b' + v + '\\b', 'g'),r[i]);
      });
      tableinfo.text(tableinfo_text);
      // var dataTables_length = datatable.closest('.dataTables_wrapper').find('.dataTables_length label');
      // var dataTables_html = dataTables_length.html();
      // var f1 = ['Show','entries'];
      // var r1 = ['Afficher', 'enregistrements'];
      // $.each(f1,function(i,v) {
      //     dataTables_html = dataTables_html.replace(new RegExp('\\b' + v + '\\b', 'g'),r1[i]);
      // });
      // dataTables_length.html(dataTables_html);


    });
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
  });
})(jQuery);