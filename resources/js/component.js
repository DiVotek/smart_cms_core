export function dialog() {
   return {
      isShow: false,
      isBackdrop: true,
      root: null,
      label: '',
      description: '',
      animationTime: 0,
      init() {
         this.root = this.$el;
         document.addEventListener('close-modal', () => {
            this.closeModal();
         })
      },
      openModal(element, label = '', description = '', animationTime = 200, isBackdrop = true) {
         this.root.appendChild(element.content.cloneNode(true));
         this.isShow = true;
         this.isBackdrop = isBackdrop;
         this.label = label;
         this.description = description;
         this.animationTime = animationTime;
         setTimeout(() => {
            this.$dispatch('state-modal-content', { state: true });
         }, 0);
      },
      closeModal() {
         this.label = '';
         this.description = '';
         this.$dispatch('state-modal-content', { state: false });
         setTimeout(() => {
            this.isShow = false;
            this.root.innerHTML = '';
         }, this.animationTime);
      },
   };
}


/**
* Possible values for trigger:
*   - 'hover': Tooltip shows on hover. dropdown({ trigger: 'hover' })
*   - 'click': Tooltip shows on click. Default
*/
export function dropdown({ trigger = 'click' } = {}) {
   return {
      isShow: false,
      self: {
         ['@mouseenter']() {
            if (trigger == 'hover') {
               this.isShow = true;
            }
         },
         ['@mouseleave']() {
            if (trigger == 'hover') {
               this.isShow = false;
            }
         },
      },
      button: {
         ['@click']() {
            if (trigger === 'click') {
               this.isShow = !this.isShow;
            }
         },
      },
      list: {
         ['@keydown.escape']() {
            this.isShow = false;
         },
         ['@click.outside']() {
            if (trigger == 'click') {
               this.isShow = false;
            }
         },
         ['x-trap']() {
            return this.isShow;
         },
         ['x-show']() {
            return this.isShow;
         },
      },
      option({ selected }) {
         return {
            ['aria-selected']: selected ? 'true' : 'false',
         };
      },
   };
}


/**
* Possible values for trigger:
*   - 'hover': Tooltip shows on hover. tooltip({ trigger: 'hover' })
*   - 'click': Tooltip shows on click. tooltip({ trigger: 'click' })
*   - 'hover-click': Tooltip shows on both hover and click. Default
*/
export function tooltip({ trigger = 'hover-click' } = {}) {
   return {
      isShow: false,
      isClick: false,
      self: {
         ['@mouseenter']() {
            if (trigger == 'hover' || trigger == 'hover-click') {
               this.isShow = true;
            }
         },
         ['@mouseleave']() {
            if (trigger == 'hover' || trigger == 'hover-click') {
               if (!this.isClick) {
                  this.isShow = false;
               }
            }
         },
         ['@click.outside']() {
            if (trigger == 'click' || trigger == 'hover-click') {
               this.isShow = false;
               this.isClick = false;
            }
         },
      },
      button: {
         ['@click']() {
            if (trigger == 'click' || trigger == 'hover-click') {
               if (!this.isClick) {
                  this.isShow = true;
                  this.isClick = true;
               } else {
                  this.isShow = false;
                  this.isClick = false;
               }
            }
         },
      },
      body: {
         ['x-show']() {
            return this.isShow;
         },
      },
   }
}
