(() => {
	const contentContainer = document.getElementById('content-container');
	const cardContainer = document.getElementById('card-container');

	const dislikeBtn = document.getElementById('dislike-swipe');
	const likeBtn = document.getElementById('like-swipe');

	function createCard(card, i) {
		sound = document.createElement('img');
		sound.classList.add('rounded-img', 'firstSound');

		if (i < 0) {
			sound.classList.add('secondSound');
		}

		sound.alt = `Image de couverture de musique : ${card.title} `;
		sound.src = card.image;
		sound.style.zIndex = i;

		cardTitle = document.createElement('h2');
		cardTitle.classList.add('text-center', 'mb-3');
		cardTitle.innerText = card.title;

		cardContainer.appendChild(sound);
	}

	/*
    async function sounds() {
        let soundsFormData = new FormData();

        soundsFormData.append('type','sounds')
        soundsFormData.append('token', token)
        await apiCall("api/user/activity", soundsFormData, async function(data) {
            if(data != "") {
                const parsedData = JSON.parse(data);

                if(parsedData != null){
                    if(parsedData['error'] != undefined) {
                        routeError(parsedData['error']);
                        return;
                    } else {
                        if(parsedData.lenth == 0) return;

                        let i = 0;
                        for(card in parsedData) {
                            createCard(card, i)
                            i++
                        }
                    }
                }
            }
        })
    }
        */

	const test = [
		{
			id: '1',
			title: 'Test',
			artist: '1',
			type: '1',
			image: 'http://localhost:8081/storage/sound/image/1.jpg',
			link: 'http://localhost:8081/storage/sound/file/1.mp3',
		},
		{
			id: '5',
			title: 'Test',
			artist: '1',
			type: '1',
			image: 'http://localhost:8081/storage/sound/image/5.png',
			link: 'http://localhost:8081/storage/sound/file/1.mp3',
		},
		{
			id: '20',
			title: 'Test',
			artist: '1',
			type: '1',
			image: 'http://localhost:8081/storage/sound/image/20.jpg',
			link: 'http://localhost:8081/storage/sound/file/1.mp3',
		},
		{
			id: '27',
			title: 'Test',
			artist: '1',
			type: '1',
			image: 'http://localhost:8081/storage/sound/image/27.png',
			link: 'http://localhost:8081/storage/sound/file/1.mp3',
		},
		{
			id: '28',
			title: 'Test',
			artist: '1',
			type: '1',
			image: 'http://localhost:8081/storage/sound/image/28.jpg',
			link: 'http://localhost:8081/storage/sound/file/1.mp3',
		},
		{
			id: '30',
			title: 'Test',
			artist: '1',
			type: '1',
			image: 'http://localhost:8081/storage/sound/image/30.png',
			link: 'http://localhost:8081/storage/sound/file/1.mp3',
		},
	];

	function generateCards(test) {
		let i = 0;
		for (card of test) {
			createCard(card, i);
			i--;
		}
	}

	const remove = async (isLiked) => {
		if (cardContainer.children.length > 0) {
			const firstCard = cardContainer.children[0];

			if (isLiked) {
				firstCard.classList.add('liked-sound');
			} else {
				firstCard.classList.add('unliked-sound');
			}

			await new Promise((resolve) => {
				firstCard.addEventListener('animationend', resolve, { once: true });
			});

			firstCard.remove();
			if (cardContainer.children.length > 0) {
				cardContainer.children[0].id = 'first-sound';

				cardContainer.children[0].classList.remove('secondSound');
			}
		}
	};

	dislikeBtn.onclick = function () {
		remove(0);
	};

	likeBtn.onclick = function () {
		remove(1);
	};

	generateCards(test);
})();
