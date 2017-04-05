/**
 * userAgent检测
 * @return {[type]}     [description]
 */
window.UA = (function(UA) {

	UA = UA || {};

	var ua = navigator.userAgent;
	if (ua.indexOf('Android') > -1) {
		UA.platform = 'android';
	} else if (/iPhone|iPad|iPod/.test(ua)) {
		UA.platform = 'ios';
	} else if (/Windows Phone|WPDesktop/.test(ua)) {
		UA.platform = 'winphone';
	} else {
		UA.platform = 'pc';
	}

	UA[UA.platform] = true;

	UA.qqVersion = (function() {
		var m = ua.match(/QQ\/([\d\.]+)/);
		return m ? m[1] : '0';
	})();

	UA.wxVersion = (function() {
		var m = ua.match(/MicroMessenger\/([\d\.]+)/);
		return m ? m[1] : '0';
	})();

	if (UA.qqVersion > '0') {
		UA.client = 'qq';
	} else if (UA.wxVersion > '0') {
		UA.client = 'wx';
	} else {
		UA.client = 'browser';
	}

	UA[UA.client] = true;

	UA.compareVersion = function(a, b) {
		a = String(a).split('.');
		b = String(b).split('.');
		try {
			for (var i = 0, len = Math.max(a.length, b.length); i < len; i++) {
				var l = isFinite(a[i]) && Number(a[i]) || 0,
					r = isFinite(b[i]) && Number(b[i]) || 0;
				if (l < r) {
					return -1;
				} else if (l > r) {
					return 1;
				}
			}
		} catch (e) {
			return -1;
		}
		return 0;
	};

	UA.compareQQ = function(ver) {
		return UA.compareVersion(UA.qqVersion, ver);
	};

	UA.compareWX = function(ver) {
		return UA.compareVersion(UA.wxVersion, ver);
	};

	return UA;
})(window.UA);