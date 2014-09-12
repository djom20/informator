//Variables
var oldtime = new Date().getTime(), WIDTH = 1000, HEIGHT = 1000, FPS = 0, STATUS = 0, usedKeys = [];

var Link = new (function (url) {
    var baseUrl = (url.substring(url.length-1) === "/")? url.substring(0, url.length-1) : url;
    
    this.Url = function (controller, action, get, strget) {
        var getTmp = "";

        for(var i=0; i <get.length; i++) {
            getTmp += "/" + get[i];
        }

        if(strget)
            if(strget.length > 0)
                strget = ((strget.substring(0, 1) === "/")? "" : "/") + strget;
        else
            strget = '';
        
        if(action.length > 0)
            action = ((action.substring(0, 1) === "/")? "" : "/") + action;

        return baseUrl + "/" + controller + action + getTmp + strget;
    };
    
    this.setBaseUrl = function (url) {
        baseUrl = (url.substring(url.length-1) === "/")? url.substring(0, url.length-1) : url;
    };
})('http://pages.spes.co');

//Image Loader
var Images = new (function () {
    var img = [], base = "", loaded = 0, count = 0;
    
    this.setBase = function (_base) {
        base = _base;
    };
    
    this.add = function (image, directory) {
        img[image] = new Image();
        img[image].src = base + directory;
    };
    
    this.get = function (image) {
        return img[image];
    };
    
    this.isLoaded = function () {
        loaded = 0;count = 0;
        for (var k in img) {
            if (img.hasOwnProperty(k)) loaded += (img[k].complete)? 1 : 0;
            count++;
        }
        
        return (loaded == count);
    };
    
    this.Percentloaded = function () {
        return loaded/count;
    };
})();

//Generate an Elastic Canvas
function Elastic (_draw, width, height) {
    var canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    var ctx = canvas.getContext('2d');
    
    this.draw = function () {
        canvas.width = canvas.width;
        _draw(ctx, canvas);
    };
    
    this.getImage = function () {
        return canvas;
    };
};

//Generate a requestAnimationFrame for any Browser
(function() {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelRequestAnimationFrame = window[vendors[x]+
        'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() {callback(currTime + timeToCall);}, 
            timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
})();

//Start the program and generate all needed
function GCanvas (cnv, wdth, hght, functions, scene) {
    
    //Some Variables used in the app
    var isLoaded = false, deltaX = 0, deltaY = 0, deltaW = 0, deltaH = 0,
        canvas = document.getElementById(cnv), rende = document.createElement('canvas');
    canvas.height = hght;
    canvas.width = wdth;
    
    var ctx = canvas.getContext("2d");
    
    HEIGHT = rende.height = hght;
    WIDTH = rende.width = wdth;
        
    var g2d = rende.getContext("2d"),
        FPS = 30, oldtime = 0,
        scenary = scene;

    //FUNCIONES DE PINTADO/CONFIGURACION DE LA WEBAPP
    var _Configs = function () {
        if(window.innerHeight !== h) {
            var h = window.innerHeight;
            var w = window.innerWidth;
            var off = $("#mycanvas").offset();
            
            deltaX = off.left;
            deltaY = off.top;
            deltaW = WIDTH;
            deltaH = HEIGHT;
        }
    };

    var _Draw = function () {
        canvas.width = canvas.width;
        try {
            ctx.drawImage(rende, 0, 0);
        } catch (ex) {
            console.log(ex);
        }
    };
    var _GameLoop = function (time) {
        
        _Configs();
        
        if(!functions[scenary].wasSetup) {
            if(functions[scenary].Setup) functions[scenary].Setup();
            functions[scenary].wasSetup = true;
        }
        
        if(!functions[scenary].isChanged) {
            if(functions[scenary].Change) functions[scenary].Change();
            functions[scenary].isChanged = true;
        }
        
        isLoaded = Images.isLoaded();
        
        if(isLoaded) {
            rende.width = rende.width;
            if(functions[scenary].Step) functions[scenary].Step();
            if(functions[scenary].Draw) functions[scenary].Draw(g2d);
        } else {
            if(functions[scenary].Loading) functions[scenary].Loading(Images.Percentloaded());
        }
        
        _Draw();
        
        FPS = (1000/(time-oldtime)).toFixed(1);
        oldtime = time;
        
        requestAnimationFrame(_GameLoop);
    };
    
    this.setScenary = function (scene) {
        scenary = scene;
        functions[scenary].isChanged = false;
    };
    
    this.getCurrentScenary = function () {
        return scenary;
    };
    
    //CANCELAR LOS EVENTOS PROPIOS DEL NAVEGADOR
//    cnv.addEventListener("touchstart", function (e) {
//        e.preventDefault();
//    }, false);
//    cnv.addEventListener("touchmove", function (e) {
//        e.preventDefault();
//    }, false);
//    cnv.addEventListener("touchend", function (e) {
//        e.preventDefault();
//    }, false);
//    cnv.addEventListener("click", function (e) {
////        e.preventDefault();
//    }, false);
//    cnv.addEventListener("mousemove", function (e) {
//        e.preventDefault();
//    }, false);
    
    //EVENTOS DEL RATON
    canvas.addEventListener("mousedown", function (e) {
        if(functions[scenary].Start) {
            var x = Math.ceil(((e.pageX-deltaX)/deltaW)*WIDTH),
                y = Math.ceil(((e.pageY-deltaY)/deltaH)*HEIGHT);
            functions[scenary].Start(x, y);
        }
    }, false);
    canvas.addEventListener("mousemove", function (e) {
        
        if(functions[scenary].Moving) {
            var x = Math.ceil(((e.pageX-deltaX)/deltaW)*WIDTH),
                y = Math.ceil(((e.pageY-deltaY)/deltaH)*HEIGHT);
            functions[scenary].Moving(x, y);
        }
    }, false);
    canvas.addEventListener("mouseup", function (e) {
        if(functions[scenary].End) {
            var x = Math.ceil(((e.pageX-deltaX)/deltaW)*WIDTH),
                y = Math.ceil(((e.pageY-deltaY)/deltaH)*HEIGHT);
            functions[scenary].End(x, y);
        }
    }, false);

    //EVENTOS DEL TOUCH
    canvas.addEventListener("touchstart", function (e) {
        if(functions[scenary].Start) {
            var touches = e.targetTouches;
            for (var i=0; i<touches.length; i++) {
                var x = Math.ceil(((touches[i].pageX-deltaX)/deltaW)*WIDTH),
                    y = Math.ceil(((touches[i].pageY-deltaY)/deltaH)*HEIGHT);
                functions[scenary].Start(x, y);
            }
        }
    }, false);
    canvas.addEventListener("touchmove", function (e) {
        if(functions[scenary].Moving) {
            var touches = e.targetTouches;
            for (var i=0; i<touches.length; i++) {
                var x = Math.ceil(((touches[i].pageX-deltaX)/deltaW)*WIDTH),
                    y = Math.ceil(((touches[i].pageY-deltaY)/deltaH)*HEIGHT);
                functions[scenary].Moving(x, y);
            }
        }
    }, false);
    canvas.addEventListener("touchend", function (e) {
        if(functions[scenary].End) {
            var touches = e.targetTouches;
            for (var i=0; i<touches.length; i++) {
                var x = Math.ceil(((touches[i].pageX-deltaX)/deltaW)*WIDTH),
                    y = Math.ceil(((touches[i].pageY-deltaY)/deltaH)*HEIGHT);
                functions[scenary].End(x, y);
            }
        }
    }, false);
    canvas.addEventListener("keydown", function (e) {
        usedKeys[e.keyCode] = true;
        if(functions[scenary].KeyDown) {
            functions[scenary].KeyDown(e.keyCode);
        }
    }, false);
    canvas.addEventListener("keyup", function (e) {
        usedKeys[e.keyCode] = false;
        if(functions[scenary].KeyUp) {
            functions[scenary].KeyUp(e.keyCode);
        }
    }, false);

    //Iniciar el pintado del juego
    requestAnimationFrame(_GameLoop);
};