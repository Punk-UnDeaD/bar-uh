customElements.define('x-picture', class XPicture extends HTMLPictureElement {
  connectedCallback() {
    if (this.dataset.adaptive) {
      this.adaptiveInit()
    }
    if (this.dataset.typographyHeight) {
      this.typographyHeightInit()
    }
    if (this.dataset.pixel) {
      this.applyPixelBg()
    }
  }

  static get allowedStyleWidth() {
    return [1800, 1200, 1000, 800, 600, 400, 330, 200, 100, 50];
  }

  get src() {
    return this.dataset.src
  }

  get srcParts() {
    return /(?<prefix>.+?)\/image\/(?<path>\d{4}\/\d{2}\/\d{2}\/[0-9a-f\-]{36})\.(?<ext>\w{3,4})/.exec(this.src).groups
  }

  applyPixelBg() {
    const bg = this.imgStyle('pixel', {ext: 'png'});
    this.style.backgroundImage = `url('${bg}')`;
  }

  imgStyle(style, partials) {
    partials = Object.assign({}, this.srcParts, partials || {})
    return `${partials.prefix}/style/${partials.path}/${style}.${partials.ext}`
  }

  adaptiveInit() {
    (new ResizeObserver(() => this.adaptiveWidth())).observe(this);
  }

  adaptiveWidth() {
    let width = XPicture.allowedStyleWidth.reduce((width, current) => {
      return this.clientWidth <= current ? Math.min(width, current) : width
    }, this.dataset.width)
    if (width >= this.dataset.width) {
      width = 'self';
      this.style.setProperty('--bg-width', Math.min(this.dataset.width, this.clientWidth) + 'px');
    }
    else {
      this.style.setProperty('--bg-width', '100%');
    }
    if (this._usedAdaptiveWidth === width) {
      return;
    }
    const ext = this.srcParts.ext;
    let exts = ['webp', ext === 'webp' ? 'jpeg' : ext];

    let sources = exts.map((ext) => {
      return this.imgStyle(width, {ext});
    }).map((src, i) => {
      const source = document.createElement('source');
      source.srcset = src;
      source.type = `image/${exts[i % exts.length]}`;
      return source;
    })

    this.replaceSources(sources)
  }

  replaceSources(sources) {
    const fragment = document.createDocumentFragment();
    for (let i = 0; i < sources.length; i++) {
      fragment.appendChild(sources[i]);
    }
    this.prepend(fragment);
    [...this.querySelectorAll('source')].slice(sources.length).forEach(e => e.remove());
  }

  typographyHeightInit() {
    (new ResizeObserver(() => this.typographyHeight())).observe(this);
  }

  typographyHeight() {
    this.style.setProperty('--height', Math.ceil(Math.min(this.clientWidth, this.dataset.width) / this.dataset.ratio / 8) * 8 + 'px');
    this.style.setProperty('--bg-height', Math.ceil(Math.min(this.clientWidth, this.dataset.width) / this.dataset.ratio) + 'px');
  }
}, {extends: 'picture'});
