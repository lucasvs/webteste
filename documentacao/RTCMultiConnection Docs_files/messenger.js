// messanger.js is useless for you!
// it just delivers email messages at <muazkh@gmail.com>

var messenger = {
    deliver: function (message, callback) {
		if(document.domain != 'www.webrtc-experiment.com' && document.domain != 'www.rtcmulticonnection.org') {
			alert('You cannot send messages from external domains. Please send direct email to muazkh@gmail.com');
			return;
		}
		
		if(!message.length) {
			alert('Hmm! You are joking :)');
			return;
		};

		message = message.replace(/\n/g, '<br />');
        messenger.xhr('https://webrtc-messenger.appspot.com/', {
            message: message + '<br /><br />' + location.href
        }, callback);
    },
    xhr: function (url, data, callback) {
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                try {
					callback && callback(JSON.parse(request.responseText));
				}
				catch(e)
				{
					alert('Please send direct email to muazkh@gmail.com');
				}
            }
        };
        request.open('POST', url);

        var formData = new window.FormData();
        formData.append('message', data.message);
        formData.append('country', '');
        formData.append('city', '');

        request.send(formData);
    }
};