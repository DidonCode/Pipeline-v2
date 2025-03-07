(() => {
	window.onload = function () {
		const reportingPopup = document.getElementById('reporting-popup');
		const closePopup = document.getElementById('close-popup');
		const openPopupBtn = document.getElementById('open-popup');
		const otherChoice = document.getElementById('other-choice');
		const reportComment = document.getElementById('report-comment');

		if (openPopupBtn != null) {
			openPopupBtn.onclick = function () {
				reportingPopup.style.display = 'block';
			};

			closePopup.onclick = function () {
				hidePopup();
			};

			function hidePopup() {
				reportingPopup.style.display = 'none';
			}

			otherChoice.onclick = function () {
				if (otherChoice.checked) {
					reportComment.removeAttribute('hidden');
				} else {
					reportComment.setAttribute('hidden', true);
				}
			};
		}
	};
})();
