import { getAllElements } from './tools';

class Filter {
	constructor() {
		this.menuItems = getAllElements('a[data-filter]');
		this.items = getAllElements('.ingredient-wrapper');
		console.log(this.menuItems);
		this.letter = '';
		this.filter = '';
		this.init();
		this.events();
	}

	init() {
		this.items.forEach((item) => {
			this.letter = item.innerText[0].toLowerCase();
			item.classList.add('cat-' + this.letter);
			this.menuItems.forEach((menuItem) => {
				console.log(this.letter);
				if (menuItem.innerText === this.letter.toUpperCase()) {
					menuItem.classList.remove('disabled');
				}
			});
		});
	}

	events() {
		this.menuItems.forEach((menuItem) => {
			if (menuItem.classList.value !== 'disabled') {
				menuItem.addEventListener(
					'click',
					this.filteredItems.bind(this)
				);
			}
		});
	}

	filteredItems(e) {
		this.menuItems.forEach((menuItem) => {
			if (menuItem.classList.contains('active')) {
				menuItem.classList.remove('active');
			}
			if (menuItem.innerText === e.target.innerText) {
				menuItem.classList.add('active');
				this.filter = menuItem.dataset.filter;
				this.items.forEach((item) => {
					if (this.filter === 'cat-*') {
						item.classList.add('onvisible');
					} else {
						if (item.classList.contains(this.filter)) {
							item.classList.add('onvisible');
						} else {
							item.classList.remove('onvisible');
						}
					}
				});
			}
		});
	}
}

export default Filter;
