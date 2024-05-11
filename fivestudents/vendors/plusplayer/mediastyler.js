$.fn.stylise = function (options) {

  const flags = {
    isSingleChannel: true,
    resetPeersOnPlay: true
  };

  const settings = $.extend({ mode: 'single-reset' }, options);
  console.log("settings: ", settings)
  switch (settings.mode) {
    case 'single-reset':
    case 1:
      flags.isSingleChannel = true;
      flags.resetPeersOnPlay = true;
      break;
    case 'single-pause':
    case 2:
      flags.isSingleChannel = true;
      flags.resetPeersOnPlay = false;
      break;
    case 'multi':
    case 3:
      flags.isSingleChannel = false;
      flags.resetPeersOnPlay = false;
      break;
    default:
      console.warn(
        `The stylised mode '${settings.mode}' is not supported.
Please instead choose from
 * single-reset - to have a single active player which resets others when played
 * single-pause - to have a single active player which pauses others when played, or
 * multi - to allow all players to be active simultaneously`);
  }

  var players = [];

  function pad(str, max) {
    str = str.toString();
    return str.length < max ? pad(`0${str}`, max) : str;
  }

  function getProgressReadout(e, d) {
    const se = parseInt(e % 60);
    const me = parseInt((e / 60) % 60);

    const sd = parseInt(d % 60);
    const md = parseInt((d / 60) % 60);

    return `${me}:${pad(se, 2)} of ${md}:${pad(sd, 2)}`;
  }

  function playFromPosition(e, id) {
    const x = e.pageX - $(`#${id} p`).offset().left;
    console.log("e- ", e)
    const player = players.find(e => e.id === id);
    console.log("player- ", player)

    const ew = $(`#${id} p`).width();
    console.log("ew- ", ew)

    const d = player.controls.duration;
    console.log("d- ", d)
    
    console.log("d * (x / ew)- ", d * (x / ew))


    console.log("player.controls.currentTime0 ", player.controls.currentTime)
    player.controls.currentTime = d * (x / ew);
    console.log("player.controls ", player.controls)
    console.log("player.controls.currentTime1 ", player.controls.currentTime)

    play(id);
  }

  function restart(id) {
    $(`.stylised-play`).show();
    $(`.stylised-pause`).hide();

    const player = players.find(e => e.id === id);
    player.controls.currentTime = 0;
    player.controls.pause();
    $(`#${player.id} .stylised-play`).show();
    $(`#${player.id} .stylised-pause`).hide();
  }

  function pause(id) {

    if (flags.isSingleChannel) {
      $(`.stylised-play`).show();
      $(`.stylised-pause`).hide();
    }

    const player = players.find(e => e.id === id);
    player.controls.pause();
    $(`#${player.id} .stylised-play`).show();
    $(`#${player.id} .stylised-pause`).hide();

  }

  function play(id) {
    for (let i = 0; i < players.length; i++) {
      const player = players[i];
      if (player.id === id) {
        console.log("player.controls.currentTime3- ", player.controls.currentTime)
        console.log("player.controls.currentTime31- ", typeof player.controls.currentTime)
        $(`#${player.id} .stylised-pause`).show();
        $(`#${player.id} .stylised-play`).hide();
        console.log("player.controls.currentTime4- ", player.controls.currentTime)
        player.controls.play();
      } else {
        if (flags.isSingleChannel) {
          $(`#${player.id} .stylised-play`).show();
          $(`#${player.id} .stylised-pause`).hide();
          player.controls.pause();

          if (flags.resetPeersOnPlay) {
            player.controls.currentTime = 0;
          }
        }
      }
    }

  }

  function updateReadout(player) {
    const c = player.controls.currentTime;
    const d = player.controls.duration;
    const r = getProgressReadout(c, d);
    $(`#${player.id} p`).text(r);
    $(`#${player.id} .stylised-time-progress`).width(`${c / d * 100}%`);
    if (c / d === 1) {
      restart(id);
    }
  }

  return this.each(function (index) {

    const src = $(this).attr('src');

    var id, getControls, replacementMarkup;

    if ($(this).hasClass('customaudio')) {
      id = `generated-audio-player-${index}`;

      getControls = () => new Audio(src);
      replacementMarkup =
        `<div class="stylised-player audio" id='${id}'>
          <div class="stylised-pause">
            <div class="stylised-pause-icon"></div>
            <h3>Pause</h3>
          </div>
          <div class="stylised-play">
            <div class="stylised-play-icon"></div>
            <h3>Listen</h3>
          </div>

          <p>Loading...</p>

          <div class="stylised-time-wrapper">
            <div class="stylised-time-progress" style="width: 0%;"></div>
          </div>

          <div class="stylised-restart"></div>

        </div>`;

    } else if ($(this).hasClass('customvideo')) {
      id = `generated-video-player-${index}`;
      getControls = () => document.getElementById(id + '-screen');
      replacementMarkup =
        `<video height="auto" src="${src}" id='${id}-screen'></video>
         <div class="stylised-player" class="video" id='${id}'>
          <div class="stylised-pause">
            <div class="stylised-pause-icon"></div>
            <h3>Pause</h3>
          </div>
          <div class="stylised-play">
            <div class="stylised-play-icon"></div>
            <h3>Listen</h3>
          </div>

          <p>Loading...</p>

          <div class="stylised-time-wrapper">
            <div class="stylised-time-progress" style="width: 0%;"></div>
          </div>

          <div class="stylised-restart"></div>

        </div>`;

    } else {
      console.warn("Element detected was not of type AUDIO or VIDEO and is not supported.");
      return;
    }

    $(this).replaceWith(replacementMarkup);
    var player = { id: id, controls: getControls() };
    $(`#${id} p, #${id} .stylised-time-wrapper, #${id} .stylised-time-progress`).click((e) => playFromPosition(e, id));
    $(`#${id} .stylised-pause`).click(() => pause(id));
    $(`#${id} .stylised-play`).click(() => play(id));
    $(`#${id} .stylised-restart`).click(() => restart(id));

    player.controls.ontimeupdate = () => { updateReadout(player); };
    player.controls.onloadedmetadata = () => { updateReadout(player); };
    player.controls.onseeking = () => { $(`#${id} p`).text("Loading..."); };
    player.controls.onseeked = () => { updateReadout(player); };
    player.controls.onended = () => {
       restart(id);
    };
    players.push(player);
  });
};