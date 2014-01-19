<!--
> Muaz Khan     - https://github.com/muaz-khan 
> MIT License   - https://www.webrtc-experiment.com/licence/
> Documentation - https://github.com/muaz-khan/WebRTC-Experiment/tree/master/RTCMultiConnection
-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Teste Audio conferencia</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <link rel="author" type="text/html" href="https://plus.google.com/100325991024054712503">
        <meta name="author" content="Muaz Khan">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="style.css">
        
        <style>
            audio {
                -moz-transition: all 1s ease;
                -ms-transition: all 1s ease;
                
                -o-transition: all 1s ease;
                -webkit-transition: all 1s ease;
                transition: all 1s ease;
                vertical-align: top;
            }

            input {
                border: 1px solid #d9d9d9;
                border-radius: 1px;
                font-size: 2em;
                margin: .2em;
                width: 30%;
            }

            .setup {
                border-bottom-left-radius: 0;
                border-top-left-radius: 0;
                font-size: 102%;
                height: 47px;
                margin-left: -9px;
                margin-top: 8px;
                position: absolute;
            }

            p { padding: 1em; }

            li {
                border-bottom: 1px solid rgb(189, 189, 189);
                border-left: 1px solid rgb(189, 189, 189);
                padding: .5em;
            }
        </style>
        <script>
            document.createElement('article');
            document.createElement('footer');
        </script>
        
        <!-- scripts used for broadcasting -->
        <script src="firebase.js"></script>
        <script src="RTCMultiConnection-v1.5.js"> </script>
		   <script src="jquery-2.0.3.min.js"> </script>
    </head>

    <body>
        <article>
            <header style="text-align: center;">
         
            </header>

        
            <!-- just copy this <section> and next script -->
            <section class="experiment">                
                <section>
                    <span>
                        Chamada privada ?? <a href="" target="_blank" title="Abra este link em uma nova aba. Em seguida, sua sala será privada!"><code><strong id="unique-token">#123456789</strong></code></a>
                    </span>
					
                    <input type="text" id="conference-name">
                    <button id="setup-new-conference" class="setup">Nova Conferencia</button>
                </section>
                
                <!-- list of all available broadcasting rooms -->
                <table style="width: 100%;" id="rooms-list"></table>
                
                <!-- local/remote audios container -->
                <div id="audios-container"></div>
				
            </section>
			 

                        <div id="chat-output"></div>
                        <input type="text" id="chat-input" style="font-size: 1.2em;display:none" placeholder="chat message" disabled>
        <div id="aud"></div>
            <script>
                // Muaz Khan     - https://github.com/muaz-khan
                // MIT License   - https://www.webrtc-experiment.com/licence/
                // Documentation - https://github.com/muaz-khan/WebRTC-Experiment/tree/master/RTCMultiConnection

				
				
				
				//funcao tokens
				var rand = function() {
					return Math.random().toString(36).substr(2); // remove `0.`
				};

				var token = function() {
					return rand() + rand(); // to make it longer
				};
				//-----
				
                var connection = new RTCMultiConnection();
                connection.session = {
                    audio: true,
					data :true
                };
                
				connection.onspeaking = function (e) {

    // e.streamid, e.userid, e.stream, etc.
    e.mediaElement.style.border = '1px solid red';
};

		connection.onaudionly = function (e) {

         $("#aud").html(e);
};

				
                connection.onstream = function(e) {
				alert("teste");
                    audiosContainer.insertBefore(e.mediaElement, audiosContainer.firstChild);
                    rotateAudio(e.mediaElement);
                };
				
				//CHAT 
				     connection.onopen = function() {
                    if (document.getElementById('chat-input')) document.getElementById('chat-input').disabled = false;      
                };
				  connection.onmessage = function(e) {
                    appendDIV(e.data);
                };
				
				var chatOutput = document.getElementById('chat-output');

                function appendDIV(data) {
                    var div = document.createElement('div');
                    div.innerHTML = data;
                    chatOutput.insertBefore(div, chatOutput.firstChild);
                    div.tabIndex = 0;
                    div.focus();
                }

                document.getElementById('chat-input').onkeypress = function(e) {
                    if (e.keyCode !== 13 || !this.value) return;
					
                    appendDIV(this.value);
                    connection.send(this.value);
                    this.value = '';
                    this.focus();
                };
				//--------

                function rotateAudio(mediaElement) {
                    mediaElement.style[navigator.mozGetUserMedia ? 'transform' : '-webkit-transform'] = 'rotate(0deg)';
                    setTimeout(function() {
                        mediaElement.style[navigator.mozGetUserMedia ? 'transform' : '-webkit-transform'] = 'rotate(360deg)';
                    }, 1000);
                }

                connection.onstreamended = function(e) {
                    e.mediaElement.style.opacity = 0;
                    rotateAudio(e.mediaElement);
                    setTimeout(function() {
                        if (e.mediaElement.parentNode) {
                            e.mediaElement.parentNode.removeChild(e.mediaElement);
                        }
                    }, 1000);
                };

                var sessions = { };
                connection.onNewSession = function(session) {
					var alreadyExists = document.getElementById(session.userid);
					if (alreadyExists) return;

					 
                    if (sessions[session.sessionid]) return;
                    sessions[session.sessionid] = session;

                    var tr = document.createElement('tr');
                    tr.innerHTML = '<td><strong>' + session.extra['session-name'] + '</strong> está em uma conferencia!</td>' +
                        '<td><button class="join">Entrar</button></td>';
                    roomsList.insertBefore(tr, roomsList.firstChild);

                    var joinRoomButton = tr.querySelector('.join');
                    joinRoomButton.setAttribute('data-sessionid', session.sessionid);
                    joinRoomButton.onclick = function() {
					   
					   $('#chat-input').show();
                        this.disabled = true;

                        var sessionid = this.getAttribute('data-sessionid');
                        session = sessions[sessionid];

                        if (!session) throw 'No such session exists.';

                        connection.join(session);
                    };
                };

                var audiosContainer = document.getElementById('audios-container') || document.body;
                var roomsList = document.getElementById('rooms-list');

                document.getElementById('setup-new-conference').onclick = function() {
                    this.disabled = true;
					connection.sessionid = token();
                    connection.extra = {
                        'session-name': document.getElementById('conference-name').value || 'Anonymous'
                    };
                    connection.open();
					$('#chat-input').show();
                };

                // setup signaling to search existing sessions
                connection.connect();

                (function() {
                    var uniqueToken = document.getElementById('unique-token');
                    if (uniqueToken)
                        if (location.hash.length > 2) uniqueToken.parentNode.parentNode.parentNode.innerHTML = '<h2 style="text-align:center;"><a href="' + location.href + '" target="_blank">Compartilhe a chamada</a></h2>';
                        else uniqueToken.innerHTML = uniqueToken.parentNode.parentNode.href = '#' + (Math.random() * new Date().getTime()).toString(36).toUpperCase().replace( /\./g , '-');
                })();



            </script>
        
        
         
    </body>
</html>
