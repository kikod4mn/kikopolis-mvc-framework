document.addEventListener('DOMContentLoaded', () => {
	const canvas           = document.getElementById('tetris');
	const nextPieceCanvas  = document.getElementById('nextPiece');
	const nextPieceContext = nextPieceCanvas.getContext('2d');
	const mainGameContext  = canvas.getContext('2d');
	const score            = document.getElementById('score');
	const borderWidth      = 0.1;
	let lastTimestamp      = 0;
	let dropCounter        = 0;
	let dropInterval       = 1000;
	const controls         = {
		ArrowDown : 'ArrowDown',
		ArrowLeft : 'ArrowLeft',
		ArrowRight: 'ArrowRight',
		a         : 'a',
		A         : 'A',
		d         : 'd',
		D         : 'D',
		s         : 's',
		S         : 'S',
		q         : 'q',
		Q         : 'Q',
		e         : 'e',
		E         : 'E',
	};
	const pieces           = { T: 'T', L: 'L', O: 'O', S: 'S', I: 'I', J: 'J', Z: 'Z' };
	const colorMap         = {
		0: { base: '#2f2f2f' },
		1: { base: '#dc4200', gradientStart: '#ffdd59', border: '#ffebcd' },
		2: { base: '#a80600', gradientStart: '#ff5e57', border: '#ffd8d6' },
		3: { base: '#0023bd', gradientStart: '#8c9fff', border: '#d9e2ff' },
		4: { base: '#008545', gradientStart: '#0be881', border: '#d0fae6' },
		5: { base: '#281b69', gradientStart: '#786fa6', border: '#cfc8ef' },
		6: { base: '#a60036', gradientStart: '#f8a5c2', border: '#f8d8e1' },
		7: { base: '#005879', gradientStart: '#4bcffa', border: '#e5f5fc' },
	};
	const pieceString      = 'TLOSIJZ';
	let nextPiece          = {
		pos   : { x: 0, y: 0 },
		matrix: [],
	};
	const player           = {
		pos   : { x: 0, y: 0 },
		matrix: [],
		score : 0,
	};
	
	const getRandomPiece = () => createPiece(pieceString[Math.floor(Math.random() * pieceString.length)]);
	
	const merge = (arena, player) => {
		player.matrix.forEach((row, y) => {
			row.forEach((value, x) => {
				if (value !== 0) {
					console.log(player);
					arena[y + player.pos.y][x + player.pos.x] = value;
				}
			});
		});
	};
	
	const collide = (arena, player) => {
		const { pos, matrix } = player;
		for (let iy = 0; iy < matrix.length; iy++) {
			for (let ix = 0; ix < matrix[iy].length; ix++) {
				if (matrix[iy][ix] !== 0
					&& (arena[iy + pos.y] !== undefined
						&& arena[iy + pos.y][ix + pos.x]) !== 0) {
					return true;
				}
			}
		}
		return false;
	};
	
	const draw = () => {
		mainGameContext.fillStyle = colorMap[0].base;
		mainGameContext.fillRect(0, 0, canvas.width, canvas.height);
		
		nextPieceContext.fillStyle = colorMap[0].base;
		nextPieceContext.fillRect(0, 0, nextPieceCanvas.width, nextPieceCanvas.height);
		
		drawMatrix(mainGameContext, arena, { x: 0, y: 0 });
		drawMatrix(mainGameContext, player.matrix, player.pos);
		
		nextPieceArena = createMatrix(6, 6);
		drawMatrix(nextPieceContext, nextPieceArena, { x: 0, y: 0 });
		drawMatrix(nextPieceContext, nextPiece.matrix, nextPiece.pos);
	};
	
	const createMatrix = (width, height) => {
		const matrix = [];
		while (height--) {
			matrix.push(new Array(width).fill(0));
		}
		return matrix;
	};
	
	const drawMatrix = (context, matrix, pos) => {
		matrix.forEach((row, y) => {
			row.forEach((value, x) => {
				if (value !== 0) {
					const gradient = context.createLinearGradient(
						x + pos.x + borderWidth, y + pos.y + borderWidth,
						x + pos.x + (1 - borderWidth), y + pos.y + (1 - borderWidth),
					);
					gradient.addColorStop(0, colorMap[value].gradientStart);
					gradient.addColorStop(1, colorMap[value].base);
					context.fillStyle   = gradient;
					context.strokeStyle = colorMap[value].border;
					context.lineWidth   = borderWidth;
					context.strokeRect(x + pos.x, y + pos.y, 1, 1);
					context.fill('evenodd');
					context.fillRect(
						x + pos.x + borderWidth / 2, y + pos.y + borderWidth / 2,
						1 - borderWidth, 1 - borderWidth,
					);
				}
			});
		});
	};
	
	const createPiece = type => {
		switch (type) {
			case pieces.T:
				return [
					[0, 0, 0],
					[1, 1, 1],
					[0, 1, 0],
				];
			case pieces.L:
				return [
					[0, 2, 0],
					[0, 2, 0],
					[0, 2, 2],
				];
			case pieces.O:
				return [
					[3, 3],
					[3, 3],
				];
			case pieces.S:
				return [
					[0, 4, 4],
					[4, 4, 0],
					[0, 0, 0],
				];
			case pieces.I:
				return [
					[0, 5, 0, 0],
					[0, 5, 0, 0],
					[0, 5, 0, 0],
					[0, 5, 0, 0],
				];
			case pieces.J:
				return [
					[0, 6, 0],
					[0, 6, 0],
					[6, 6, 0],
				];
			case pieces.Z:
				return [
					[7, 7, 0],
					[0, 7, 7],
					[0, 0, 0],
				];
		}
	};
	
	const play = (timestamp = 0) => {
		const elapsed = timestamp - lastTimestamp;
		lastTimestamp = timestamp;
		dropCounter += elapsed;
		if (dropCounter > dropInterval) {
			playerDrop();
		}
		draw();
		requestAnimationFrame(play);
	};
	
	const playerMove = (direction) => {
		player.pos.x += direction;
		if (collide(arena, player) === true) {
			player.pos.x -= direction;
		}
	};
	
	const playerDrop = () => {
		player.pos.y++;
		if (collide(arena, player) === true) {
			player.pos.y--;
			merge(arena, player);
			playerReset();
			arenaSweep();
			updateScore();
			drawNextPiece();
		}
		dropCounter = 0;
	};
	
	const playerRotate = direction => {
		const pos  = player.pos.x;
		let offset = 1;
		rotate(player.matrix, direction);
		while (collide(arena, player) === true) {
			player.pos.x += offset;
			offset = -(offset + (offset > 0 ? 1 : -1));
			if (offset > player.matrix[0].length) {
				rotate(player.matrix, -direction);
				player.pos.x = pos;
				return;
			}
		}
	};
	
	const nextPiecePos = () => {
		return {
			x: Math.floor(nextPieceArena[0].length / 2) - Math.floor(nextPiece.matrix[0].length / 2),
			y: Math.floor(nextPieceArena.length / 2) - Math.floor(nextPiece.matrix.length / 2),
		};
	};
	
	const playerReset = () => {
		player.matrix    = nextPiece.matrix;
		player.pos       = {
			x: Math.floor(arena[0].length / 2) - Math.floor(player.matrix[0].length / 2),
			y: 0,
		};
		nextPiece.matrix = getRandomPiece();
		nextPiece.pos    = nextPiecePos();
		if (collide(arena, player) === true) {
			arena.forEach(row => row.fill(0));
			player.score = 0;
			updateScore();
		}
	};
	
	const rotate = (matrix, direction) => {
		for (let y = 0; y < matrix.length; y++) {
			for (let x = 0; x < y; x++) {
				[matrix[x][y], matrix[y][x]] = [matrix[y][x], matrix[x][y]];
			}
		}
		if (direction > 0) {
			matrix.forEach(row => row.reverse());
		} else {
			matrix.reverse();
		}
	};
	
	const arenaSweep = () => {
		let rowCount = 1;
		outer :for (let y = arena.length - 1; y > 0; y--) {
			for (let x = 0; x < arena[y].length; x++) {
				if (arena[y][x] === 0) {
					continue outer;
				}
			}
			arena.unshift(arena.splice(y, 1)[0].fill(0));
			y++;
			player.score += rowCount * 10;
			rowCount *= 2;
		}
	};
	
	const updateScore = () => {
		score.innerText = player.score;
	};
	
	const onKeyDown = evt => {
		evt.preventDefault();
		switch (evt.key) {
			case controls.ArrowRight:
			case controls.D:
			case controls.d:
				playerMove(1);
				break;
			case controls.ArrowLeft:
			case controls.a:
			case controls.A:
				playerMove(-1);
				break;
			case controls.ArrowDown:
			case controls.s:
			case controls.S:
				playerDrop();
				break;
			case controls.q:
			case controls.Q:
				playerRotate(-1);
				break;
			case controls.e:
			case controls.E:
				playerRotate(1);
				break;
		}
	};
	
	const drawNextPiece = () => {
		merge(nextPieceArena, nextPiece);
	};
	
	window.addEventListener('keydown', onKeyDown);
	
	mainGameContext.scale(20, 20);
	nextPieceContext.scale(20, 20);
	
	const arena        = createMatrix(12, 20);
	let nextPieceArena = createMatrix(8, 8);
	
	player.matrix    = getRandomPiece();
	nextPiece.matrix = getRandomPiece();
	nextPiece.pos    = nextPiecePos();
	
	drawNextPiece();
	
	play();
});