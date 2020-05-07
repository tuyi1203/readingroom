

;(function($){
  var Accordion = function (obj,option){
    var self = this ;
    this.obj    = obj;
    this.option = option;
    this.LessThanIE8 = /MSIE 6.0|MSIE 7.0|MSIE 8.0/gi.test(window.navigator.userAgent);
    this.defaults = {
      imageObj      : [],
      arrangement   : "horizontal", //verticality
      fontCut       : 500,
      defaultWidth  : 100,
      defaultHeight : 100,
      animateWidth  : 600,
      animateHeight : 200,
      response      : true,
      styleColor    : {"background":"#fff","font":"#333333","textShadow":"0px 0px 1px green"},
      animateTime   : "0.5s"
    };
    $.extend(this.defaults,this.option);

    this.defaultDOM();
    self.eachImgWidth= 100;

    var img = new Image();
    $.each(this.defaults.imageObj,function(i,src){
      img.src = self.defaults.imageObj[i];
      $(img).on('load error',function()
      {
        self.winWidth    = self.obj.find('ul[data-name="accordion"]').innerWidth();
      });
    })

    this.winWidth    = this.obj.find('ul[data-name="accordion"]').innerWidth();
    this.li          = this.obj.find('.accordion-li');
    this.icon        = this.obj.find('.accordion-icon');
    this.description = this.obj.find('.accordion-description');
    this.title       = this.obj.find('.accordion-title');
    this.content     = this.obj.find('.accordion-content');
    this.Div         = this.obj.parents('.accordion-DIV');
    this.len         = this.defaults.imageObj.length;
    this.eachSmallImgWidth = this.defaults.defaultWidth;

    this.icon.css('color',this.defaults.styleColor.font);
    this.description.css('backgroundColor',this.defaults.styleColor.background);
    this.title.css('textShadow',this.defaults.styleColor.textShadow);
    this.content.css('textShadow',this.defaults.styleColor.textShadow);

    this.responseDOM();
    this.cutOverflowString();
    // Animate

    if(this.defaults.arrangement === "horizontal"){

      this.obj.hover(function() {
        $(this).find(".accordion-li").css("transition","width "+ self.defaults.animateTime +"");
      });
      var response =  (this.defaults.response)?self.eachImgWidth:self.defaults.defaultWidth;
      this.li.hover(function(e) {
        $(this).siblings('.accordion-li').width(self.eachSmallImgWidth);
        $(this).addClass('active').css("width",self.defaults.animateWidth);
        if(self.LessThanIE8){
          $(this).find(".accordion-description").css("background",self.defaults.styleColor.background).parents('.accordion-li').siblings()
          .find(".accordion-description").css("background",self.defaults.styleColor.background);
        }else{
          $(this).find(".accordion-description").css("background","linear-gradient("+self.colorMD5Count(self.defaults.styleColor.background,0).rgba+","+ self.defaults.styleColor.background +")");
        }
      },function(e){

        $(this).removeClass('active');
        self.li.css("width",response);
        $(this).find(".accordion-description").css("background","linear-gradient("+self.defaults.styleColor.background+","+ self.defaults.styleColor.background +")");

      })
    }else if(this.defaults.arrangement === "verticality"){
        this.Div.css('overflow','inherit');
        this.li.on('click',function(){
          if($(this).hasClass('active')){
            $(this).css("height",self.defaults.defaultHeight).removeClass('active')
            $(this).find(".accordion-description").css({
              "background":"linear-gradient("+self.colorMD5Count(self.defaults.styleColor.background,100).rgba+","+ self.defaults.styleColor.background +")",
              "transition":"all "+self.defaults.animateTime
            });

          }else{
            $(this).addClass('active').siblings().css("height",self.defaults.defaultHeight).removeClass('active');
            $(this).css('height', self.defaults.animateHeight);
            $(this).find(".accordion-description").css({
              "background":"linear-gradient("+self.colorMD5Count(self.defaults.styleColor.background,0).rgba+","+ self.defaults.styleColor.background +")",
              "transition":"all "+self.defaults.animateTime
            });

          };
        });
    }

  };

  Accordion.prototype = {
    defaultDOM:function(){
      var imgArr = this.defaults.imageObj,
          liHtml = '';
      if(!imgArr) return;
      $.each( imgArr , function(index, val) {
        val.alt     = val.alt || null;
        val.icon    = val.icon || null;
        val.title   = val.title || null;
        val.content = val.content || null;
        val.url     = val.url || "javascript:void(0)";
        liHtml += 
        '<li class="accordion-li">'+
          //''+
          '<a href="'+ val.url +'" target="_blank">'+
            '<i class="accordion-icon fa ">'+ val.icon +'</i><img class="accordion-img" src="'+ val.src +'" alt="'+ val.alt +'"/>'+
            '<div class="accordion-description">'+
              '<h4 class="accordion-title">'+ val.title +'</h4>'+
              '<p class="accordion-content">'+ val.content +'</p>'+
            '</div>'+
          '</a>'+

        '</li>';
      });
      var ul = $("<ul data-name='accordion' class='accordion " + this.defaults.arrangement + "'></ul>");
      var i = this.obj.append(ul.append(liHtml));
      i.wrap('<div class="accordion-DIV" />');
    },
    cutOverflowString:function(){
      var _this_ = this ; 
      this.content.each(function(index, el) {
        var len = $(el).text().length;
        if(len > _this_.defaults.fontCut){
          var sub = $(el).text().substring(0,_this_.defaults.fontCut);
          $(el).text(sub + '...');
        }
      });
    },
    responseDOM:function(){
      if(this.defaults.arrangement === "verticality"){
        this.li.css({'width': '100%','height':this.defaults.defaultHeight});
      }else if(this.defaults.response && this.defaults.arrangement === "horizontal"){
        var imgArr = this.defaults.imageObj,
            bigImgWidth = this.defaults.animateWidth,
            len    = imgArr.length;
        this.eachImgWidth =  this.winWidth / len; 
        this.li.width(this.eachImgWidth);
        this.eachSmallImgWidth = ( this.winWidth - bigImgWidth ) / (len-1);

        this.obj.find('ul[data-name="accordion"]').width(this.winWidth + this.winWidth/100);
      }else{
        this.li.css('width', this.defaults.defaultWidth);
        this.obj.find('ul[data-name="accordion"]').width(this.winWidth*2);
      }
    },
    //#fff 转为 rgab()
    colorMD5Count:function(hex, al){
      var hexColor = /^#/.test(hex) ? hex.slice(1) : hex,
          alp = hex === 'transparent' ? 0 : Math.ceil(al),
          r, g, b;
      hexColor = /^[0-9a-f]{3}|[0-9a-f]{6}$/i.test(hexColor) ? hexColor : 'fffff';
      if (hexColor.length === 3) {
          hexColor = hexColor.replace(/(\w)(\w)(\w)/gi, '$1$1$2$2$3$3');
      }
      r = hexColor.slice(0, 2);
      g = hexColor.slice(2, 4);
      b = hexColor.slice(4, 6);
      r = parseInt(r, 16);
      g = parseInt(g, 16);
      b = parseInt(b, 16);
      return {
          hex: '#' + hexColor,
          alpha: alp,
          rgba: 'rgba(' + r + ', ' + g + ', ' + b + ', ' + (alp / 100).toFixed(2) + ')'
      };
    }
  };

  $.fn.extend({
    accordion:function(option){
      new Accordion(this,option);
    }
  })

})(jQuery);
