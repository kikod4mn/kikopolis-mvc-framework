const CHOICE_DATA          = {
	ROCK    : { name: 'rock', defeats: 'scissors', iconText: '$', iconClass: 'gem' },
	PAPER   : { name: 'paper', defeats: 'rock', iconText: '€', iconClass: 'scroll' },
	SCISSORS: { name: 'scissors', defeats: 'paper', iconText: '%', iconClass: 'cut' },
};
const choices              = [CHOICE_DATA.ROCK.name, CHOICE_DATA.PAPER.name, CHOICE_DATA.SCISSORS.name];
const rockBtn              = document.getElementById('rock');
const paperBtn             = document.getElementById('paper');
const scissorsBtn          = document.getElementById('scissors');
const tryAgainBtn          = document.getElementById('tryAgain');
const resetGameBtn         = document.getElementById('resetGame');
const playerScoreEl        = document.getElementById('playerScore');
const cpuScoreEl           = document.getElementById('cpuScore');
const messageEl            = document.getElementById('message');
const controlsEl           = document.getElementById('controls');
const playerChoice         = document.getElementById('playerChoice');
const playerChoiceSpan     = document.getElementById('playerChoiceSpan');
const cpuChoice            = document.getElementById('cpuChoice');
const cpuChoiceSpan        = document.getElementById('cpuChoiceSpan');
const casinoRollers        = document.querySelectorAll('.casino__roll');
const casinoRollerWrappers = document.querySelectorAll('.casino__roll--wrapper');
const winnerColor          = '#1ec712';
const loserColor           = '#ff0e46';
const drawColor            = '#1c95e5';
const colors               = [drawColor, loserColor, winnerColor];
let result;
let playerScore            = 0;
let cpuScore               = 0;
const animationSpeed       = 400;

playerScoreEl.innerText = `${ playerScore }`;
cpuScoreEl.innerText    = `${ cpuScore }`;

const initGame = () => {
	resetGame();
	setAllCasinoRollerWrapperHeights();
};

const playGame = choice => {
	// clear messages at start of every roll
	clearAllMessages();
	renderMessage('default', 'Please wait for the computer roll to finish');
	// show rolling animation of timer type with icons as computer "chooses"
	// win condition and scoring
	const playerChoice = CHOICE_DATA[`${ choice.toUpperCase() }`];
	const cpuChoice    = getCpuChoice();
	const result       = calculateResult(playerChoice, cpuChoice);
	renderPlayerChoice(playerChoice);
	// all animation for cpu scrolling is in one monolith function
	animateCpuRolling(cpuChoice, result);
	// show the cpu score on time with the animation end
	setTimeout(() => renderCpuChoice(cpuChoice), animationSpeed * 8);
	// update scores only when rolling is done!!!!!
	setTimeout(() => {
		clearAllMessages();
		if (result === 1) {
			playerScore++;
			setWinner('player');
		} else if (result === -1) {
			cpuScore++;
			setWinner('cpu');
		} else {
			setWinner('draw');
		}
		updateScores();
	}, animationSpeed * 8);
};

const calculateResult = (playerChoice, cpuChoice) => {
	if (playerChoice.defeats === cpuChoice.name) {
		result = 1;
	}
	if (cpuChoice.defeats === playerChoice.name) {
		result = -1;
	}
	return result;
};

const setWinner = winner => {
	switch (winner) {
		case 'player':
			renderMessage('success', 'You win!!!');
			playerChoice.classList.add('winner');
			cpuChoice.classList.add('loser');
			break;
		case 'cpu':
			renderMessage('error', 'Oh no! The CPU wins!!!');
			cpuChoice.classList.add('winner');
			playerChoice.classList.add('loser');
			break;
		case 'draw':
			renderMessage('info', 'How incredible! A draw! Neither get a point.');
			playerChoice.classList.add('draw');
			cpuChoice.classList.add('draw');
			break;
		default:
			renderMessage('error', 'An error has occurred! Please refresh the page and try again.');
			break;
	}
};

const renderPlayerChoice = choice => {
	setChoiceToRenderSpan(playerChoiceSpan, choice);
};

const renderCpuChoice = choice => {
	setChoiceToRenderSpan(cpuChoiceSpan, choice);
};

const showCasinoRollers = () => {
	casinoRollerWrappers.forEach(roller => {
		roller.classList.remove('hidden');
		setCasinoRollerInnerToDefault(roller);
	});
};

const hideCasinoRollers = () => {
	casinoRollerWrappers.forEach(roller => {
		roller.classList.add('hidden');
	});
};

const setCasinoRollerInnerToDefault = roller => {
	roller.classList.remove('cpu__choice__showing');
	roller.innerHTML = `<span class="casino__roll"><i class="fas fa-gem"></i></span>
			<span class="casino__roll"><i class="fas fa-scroll"></i></span>
			<span class="casino__roll"><i class="fas fa-cut"></i></span>`;
};

const setCasinoRollerTextToChoice = (roller, choice) => {
	roller.innerHTML = '';
	roller.classList.add('cpu__choice__showing');
	const i            = document.createElement('i');
	i.className        = `fas fa-${ choice.iconClass }`;
	const testSpan     = document.createElement('span');
	testSpan.className = 'casino__roll';
	// debug for no internet
	// testSpan.innerText = choice.iconText;
	roller.appendChild(i);
	roller.appendChild(testSpan);
};

const getRandomColor = () => {
	return colors[Math.floor(Math.random() * colors.length)];
};

const setAllCasinoRollerWrapperHeights = () => {
	casinoRollerWrappers.forEach(roller => {
		roller.style.height = `${ rockBtn.offsetHeight }px`;
	});
};

const animateCpuRolling = cpuChoice => {
	// set the ending bg color depending on the result, all roller bgs will color green, red or blue
	const color = getColorByResult(result);
	// fade out the choice buttons
	fadeOutElement(rockBtn);
	fadeOutElement(paperBtn);
	fadeOutElement(scissorsBtn);
	// after the controls element has faded out, make DOM changes
	setTimeout(() => {
		// hide choice buttons and try again
		hideTryAgainAndReset();
		hideChoiceButtons();
		// make casino rollers visible
		showCasinoRollers();
	}, animationSpeed);
	// for some reason fading controls back in must be done a little bit more after fade out finishes
	setTimeout(() => {
		for (const crWrapper of casinoRollerWrappers) {
			fadeInElement(crWrapper);
		}
	}, animationSpeed);
	
	// after we have shown rollers, we need to wait to start the rolling animation
	setTimeout(() => {
		// animate rolling and set end timeouts
		for (let i = 0; i < casinoRollerWrappers.length; i++) {
			const roller = casinoRollerWrappers[i];
			// animate all buttons with different timings for 3-4 sec
			setTimeout(() => roller.classList.add('animating'), i * 100);
			// remove animation with intervals as well, leave about 2 seconds of time to show final cpu choice
			setTimeout(() => {
				roller.classList.remove('animating');
				setCasinoRollerTextToChoice(roller, cpuChoice);
			}, (animationSpeed * 5) - (i * 100));
			// set animating interval for wrapper bg color
			const interval = setInterval(() => setCasinoRollerWrapperBgColor(roller, getRandomColor()), 100);
			setTimeout(() => clearInterval(interval), animationSpeed * 5);
			// set all wrappers to one color after animation
			setTimeout(() => setCasinoRollerWrapperBgColor(roller, color), animationSpeed * 5);
		}
	}, animationSpeed * 1.5);
	// fade out casino rollers
	setTimeout(() => {
		for (const crWrapper of casinoRollerWrappers) {
			fadeOutElement(crWrapper);
		}
	}, animationSpeed * 9);
	
	setTimeout(() => {
		// hide all controls
		hideTryAgainAndReset();
		hideChoiceButtons();
		// set rollerWrapper bg color to default
		for (const roller of casinoRollerWrappers) {
			setCasinoRollerWrapperBgColor(roller);
		}
		hideCasinoRollers();
		// fade in try again and reset buttons
		showTryAgainAndResetButtons();
		fadeInElement(tryAgainBtn);
		fadeInElement(resetGameBtn);
	}, animationSpeed * 10);
};

const getColorByResult = result => {
	if (result === 1) return winnerColor;
	if (result === -1) return loserColor;
	return drawColor;
};

const setCasinoRollerWrapperBgColor = (roller, color = '#1ec712') => {
	roller.style.backgroundColor = color;
};

const getCpuChoice = () => {
	return CHOICE_DATA[`${ choices[Math.floor(Math.random() * choices.length)].toUpperCase() }`];
};

const updateScores = () => {
	playerScoreEl.innerText = `${ playerScore }`;
	cpuScoreEl.innerText    = `${ cpuScore }`;
};

const showChoiceButtons = () => {
	rockBtn.classList.remove('hidden');
	paperBtn.classList.remove('hidden');
	scissorsBtn.classList.remove('hidden');
};

const showTryAgainAndResetButtons = () => {
	tryAgainBtn.classList.remove('hidden');
	resetGameBtn.classList.remove('hidden');
};

const resetGame = () => {
	// reset and redraw scores
	playerScore = 0;
	cpuScore    = 0;
	updateScores();
	tryAgain();
};

const tryAgain = () => {
	// reset result
	result = undefined;
	// fade out try again and reset buttons
	fadeOutElement(tryAgainBtn);
	fadeOutElement(resetGameBtn);
	// hide try again and reset and rollers and show choice buttons
	setTimeout(hideTryAgainAndReset, animationSpeed);
	hideCasinoRollers();
	// fade in choice buttons
	setTimeout(() => {
		showChoiceButtons();
		fadeInElement(rockBtn);
		fadeInElement(paperBtn);
		fadeInElement(scissorsBtn);
	}, animationSpeed);
	// reset choice rendering
	setChoiceRendersToDefaultClasses();
	setChoiceRenderSpansToDefaultContent();
	// reset to starting message
	clearAllMessages();
	renderMessage('default', 'Make your choice!! Click the blue buttons to choose.');
};

const hideChoiceButtons = () => {
	rockBtn.classList.add('hidden');
	paperBtn.classList.add('hidden');
	scissorsBtn.classList.add('hidden');
};

const hideTryAgainAndReset = () => {
	tryAgainBtn.classList.add('hidden');
	resetGameBtn.classList.add('hidden');
};

const setChoiceRendersToDefaultClasses = () => {
	playerChoice.className = 'choice player__choice';
	cpuChoice.className    = 'choice cpu__choice';
};

const setChoiceToRenderSpan = (span, choice) => {
	const i            = document.createElement('i');
	i.className        = `fas fa-${ choice.iconClass }`;
	const testSpan     = document.createElement('span');
	// debug for no internet
	// testSpan.innerText = choice.iconText;
	span.appendChild(i);
	span.appendChild(testSpan);
};

const setChoiceRenderSpansToDefaultContent = () => {
	playerChoiceSpan.innerHTML = '&nbsp;';
	cpuChoiceSpan.innerHTML    = '&nbsp;';
};

const renderMessage = (type, message) => {
	const children = [];
	for (const child of messageEl.children) {
		children.push(child);
	}
	const msg     = document.createElement('span');
	msg.className = `message message__${ type }`;
	msg.innerText = message;
	children.unshift(msg);
	// reset innerHtml and add back the children
	messageEl.innerHTML = '';
	for (const child of children) {
		messageEl.appendChild(child);
	}
};

const clearAllMessages = () => {
	for (const message of messageEl.children) {
		fadeOutElement(message);
		setTimeout(() => message.remove(), animationSpeed);
	}
};

const fadeInElement = element => {
	element.classList.add('fade__in');
	// remove the class so future animations would work!
	setTimeout(() => element.classList.remove('fade__in'), animationSpeed);
};

const fadeOutElement = element => {
	element.classList.add('fade__out');
	// remove the class so future animations would work!
	setTimeout(() => element.classList.remove('fade__out'), animationSpeed);
};

initGame();

rockBtn.addEventListener('click', () => playGame(CHOICE_DATA.ROCK.name));
paperBtn.addEventListener('click', () => playGame(CHOICE_DATA.PAPER.name));
scissorsBtn.addEventListener('click', () => playGame(CHOICE_DATA.SCISSORS.name));
tryAgainBtn.addEventListener('click', tryAgain);
resetGameBtn.addEventListener('click', resetGame);