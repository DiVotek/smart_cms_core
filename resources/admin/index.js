document.addEventListener("DOMContentLoaded", function () {
   let groups = [];
   for (let nav of document.querySelector('.fi-sidebar-nav-groups').querySelectorAll('li')) {
      groupName = nav.getAttribute('data-group-label')
      if (groupName) {
         groups.push(groupName)
      }
      if (nav.querySelector('div') === null) {
         continue
      }
      nav.querySelector('div').addEventListener('click', function () {
         console.log('Clicked');

         for (let group of groups) {
            if (group == nav.getAttribute('data-group-label')) {
               continue
            }
            Alpine.store('sidebar').collapseGroup(group)
         }
      })
   }

})
