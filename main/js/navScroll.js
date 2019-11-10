(function()
{
    window.onscroll = function() {navScroll()};

    var navbar = document.getElementsByClassName("navbar")[0];
    var sticky = navbar.offsetTop;
    
    function navScroll() 
    {
        if (window.pageYOffset >= sticky)
        {
            navbar.classList.add("sticky")
        } 
        else 
        {
            navbar.classList.remove("sticky");
        }
    }
    navScroll();
})();
