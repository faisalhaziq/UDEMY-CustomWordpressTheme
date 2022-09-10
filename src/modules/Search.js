import $ from 'jquery'
class Search{
    // Init the Object
    constructor(){
        this.addSearchHTML();
        this.resultsDiv = $("#search-overlay__results");    
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchTerm = $("#search-term");    
        this.events();
        this.isOpen = false;
        this.typingTimer;
        this.isSpinning = false;
        this.previousVal;
    }

    // Events
    events(){
        this.openButton.on("click", this.openOverlay.bind(this));
        this.closeButton.on("click", this.closeOverlay.bind(this));
        $(document).on("keydown", this.keyPressDispatcher.bind(this));
        this.searchTerm.on("keyup", this.typingLogic.bind(this));
    }

    // Functions
    typingLogic(){
        if(this.searchTerm.val()!= this.previousVal){
            clearTimeout(this.typingTimer);
            
            if(this.searchTerm.val()){
                if(!this.isSpinning){
                    this.resultsDiv.html('<div class="spinner-loader"></div>');
                    this.isSpinning = true;
                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 300);
            }else{
                this.resultsDiv.html('');
                this.isSpinning = false;
            }
            
        }
        this.previousVal = this.searchTerm.val();
    }

    getResults(){
        $.getJSON(unidata.root_url + '/wp-json/university/search?term=' + this.searchTerm.val(), (results)=>{
            this.resultsDiv.html(
                `
                <div class="row">
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">General Information</h2>
                        ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search term.</p>'}
                        ${results.generalInfo.map(item=> `<li><a href="${item.permalink}">${item.title}</a> ${item.type=='post' ? ` by ${item.author}` : ''}</li>`).join('')}
                        ${results.generalInfo.length ? '</ul>' : ''}
                    </div>

                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Programs</h2>
                        ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No programs matches that search term. <a href="${unidata.root_url}/programs">View All Programs</a></p> `}
                        ${results.programs.map(item=> `<li><a href="${item.permalink}">${item.title}</a> </li>`).join('')}
                        ${results.programs.length ? '</ul>' : ''}
                        
                        <h2 class="search-overlay__section-title">Professors</h2>
                        ${results.professors.length ? '<ul class="professor-cards">' : `<p>No professors matches that search.</p> `}
                        ${results.professors.map(item=> `
                        <li class="professor-card__list-item">
                            <a class="professor-card" href="${item.permalink}">
                            <img class="professor-card__image" src="${item.image}" alt="">
                            <span class="professor-card__name">
                                ${item.title}
                            </span>
                            </a>
                        </li>
                        `).join('')}
                        ${results.professors.length ? '</ul>' : ''}
                    </div>

                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Events</h2>
                        ${results.events.length ? '' : `<p>No events matches that search term. <a href="${unidata.root_url}/events">View All Events</a></p> `}
                        ${results.events.map(item=> `
                        <div class="event-summary">
                            <a class="event-summary__date t-center" href="${item.permalink}">
                                <span class="event-summary__month">${item.month}</span>
                                <span class="event-summary__day">${item.day}</span>
                            </a>
                            <div class="event-summary__content">
                                <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                                <p>${item.desc}<a href="${item.permalink}" class="nu gray"> Read more</a></p>
                            </div>
                        </div>
                        `).join('')}
                    </div>
                </div>
                `
            );
            this.isSpinnerVisible = false;
        });
    }

    openOverlay(){
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.searchTerm.val('');
        setTimeout(()=> this.searchTerm.focus(), 301);
        this.isOpen = true;
    }

    closeOverlay(){
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.isOpen = false;
    }

    keyPressDispatcher(e){
        if(e.keyCode == 83 && !this.isOpen && !$("input, textarea").is(':focus')){
            this.openOverlay();
        }

        if(e.keyCode == 27 && this.isOpen){
            this.closeOverlay();
        }
    }

    addSearchHTML(){
        $("body").append(
        `
        <div class="search-overlay">
            <div class="search-overlay__top">
                <div class="container">
                    <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                    <input type="text" id="search-term" placeholder="Whatcha searchin for?" class="search-term" autocomplete="off">
                    <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                </div>
            </div>

            <div class="container">
                <div id="search-overlay__results">
                    
                </div>
            </div>
        </div>
        `  
        );
    }
}

export default Search