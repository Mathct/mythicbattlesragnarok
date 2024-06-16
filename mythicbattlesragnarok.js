/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * mythicbattlesragnarok implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * mythicbattlesragnarok.js
 *
 * mythicbattlesragnarok user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.mythicbattlesragnarok", ebg.core.gamegui, {
        constructor: function(){         
            this.zoomScales = [ 600, 950, 1200, 2000];
            this.zoomIndex = 0;
            this.circleRadius = 50;
        	this.cardWidth = 132; 
            this.cardTypes = {};
            var bodycoords = dojo.marginBox("game_play_area");
            var contentWidth = bodycoords.w;
            if(contentWidth>2650)
            {
                this.zoomIndex = 3;
            }
            else  if(contentWidth>1850)
            {
                this.zoomIndex = 2;
            }
            else  if(contentWidth>1500)
            {
                this.zoomIndex = 1;
            }
            this.reglettes = {11: 15, 10 : 41, 9 : 58, 8 : 66, 7 : 81, 6 : 103, 5 : 105};

            this.status = {
                "activated":_("has already been activated"),
                "complex":_("has started a complex action"),
                "notalent":_("cannot use talents"),
                "noACTIVE":_("cannot use active powers"),
                "noPASSIVE":_("cannot use passive powers"),
                "noOFFENSIVE":_("cannot use offensive powers"),
                "currentactivation":_("currently activated"),
                "skuld":_("needs an extra aow card to be activated"),

                "walked":_("has already walked"),
                "attacked":_("has already attacked"),
                "retaliated":_("has already retaliated"),
                "claimed":_("has already claimed"),
                "absorbed":_("has already absorbed"),
                "run":_("has already run"),

                "nowalk":_("cannot walk"),
                "norun":_("cannot claim"),
                "noattack":_("cannot attack"),
                "noclaim":_("cannot claim"),
                "noabsorb":_("cannot absorb"),
                "noascend":_("cannot ascend"),
                "noreroll":_("cannot reroll"),
                "noblock":_("ignores block talent"),
                "offensep1":_("offense +1"),
                "defensep1":_("defense +1"),
                "rangep1":_("range +1"),
                "movementp1":_("movement +1"),
                "noascend":_("cannot ascend"),
                "noforce":_("cannot be moved by other player"),
                "ignoreWater":_("ignore water terrain effects"),
                "ignoreBurning":_("ignore burning terrain effects"),
                "ignorePolar":_("ignore polar terrain effects"),
                "ignoreTalent":_("ignore all talents"),
                "ignorePower":_("ignore all powers"),
                "noredirect":_("this unit's attacks cannot be redirected"),
                "forcewater":_("is forced to move in water"),

                "power0":_("has already use its first power"),
                "power1":_("has already use its second power"),
                "power2":_("has already use its third power"),
                "nopower0":_("cannot use its first power"),
                "nopower1":_("cannot use its second power"),
                "nopower2":_("cannot use its third power"),
                "zonetarget":_("will be the target of a zone attack"),
                "mobility":_("can walk after having carried out an attack"),
                "oneSimple":_("can perform only one simple action"),
            };
        },
        
        adaptViewportSize : function() {
            var size = this.zoomScales[this.zoomIndex];
            var percentageOn1 = size / 2000;                        
             dojo.style("gboard", "transform", "scale(" + percentageOn1 + ")");
             dojo.style("gboard",'width',size+'px');
             dojo.style("gboard",'height',(size+15)+'px');
             dojo.style("frontboard",'width',size+'px');
             dojo.style("frontboard",'height',(size)+'px');
             dojo.style("dices",'width',size+'px');
             dojo.style("dices",'height',(size)+'px');
             dojo.style("discardshow",'width',size+'px');
             dojo.style("discardshow",'height',(size)+'px');
             if(document.getElementById('draft') != null)
             {
                dojo.style("draft",'width',size+'px');
                dojo.style("draft",'height',(size)+'px');
             }
        },

        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {             
            document.getElementById('board').classList.add('board'+gamedatas.board);
            this.players = gamedatas.players;
            this.debug = gamedatas.debug;

            // Setting up player boards
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
        		dojo.place(this.format_block('jstpl_score', player),$('player_board_'+player['id']));  
                
                var side = player.player_no == 1?"bottom":'top';
                if(!this.isSpectator && this.players[this.player_id].player_no == 2)
                {
                    side = player.player_no == 1?"top":'bottom';
                }
                dojo.place('<div  class="rp"><span id="rp'+player.player_no+'">'+player.rp+'</span> &nbsp;'+_('RP left')+'</div>',$('side'+side));
            
                for(var i in player.starting)
                {
                    var zone =  document.getElementById("zone"+player.starting[i]);
                    zone.setAttribute('style', 'fill: #'+ player.player_color +'55 !important');
                }

            }

            this.cardTypes[0] = {'name':'', 'category':'autre','card_type':0, 'card_type_arg':0};
            this.cardTypes[-1] = {'name':'', 'category':'autre', 'card_type':-1, 'card_type_arg':-1};
            for( var unit_id in gamedatas.units )
            {
                var unit = gamedatas.units[unit_id];
                this.cardTypes[unit['type']] = {'name':_(unit['name']), 'category':unit['category'], 'card_type':unit.type, 'card_type_arg':unit.id};
                if(unit.player_id == 0)
                {
                    unit['color'] = "unknown";
                }
                else{
                    unit['color'] = this.players[unit.player_id].player_color;
                }

                unit['imgfull'] = g_gamethemeurl+"img/unit"+unit.type+".png";
                unit['top'] = this.reglettes[unit.stats.length] + (unit.stats.length - unit['hp']) * 25.25;
                unit['nbmeeple']= unit.stats.length;

                if(unit.zone_id != 0)
                {
                    if(unit.zone_id>0)
                    {
                        dojo.place(this.format_block('jstpl_unittoken', unit),$('board'));
                        const pos = this.getAvailablePosition("zone"+unit.zone_id);
                        var unithtml =  document.getElementById("unit"+unit.id);
                        unithtml.style.left = (pos.X - this.circleRadius) + "px";
                        unithtml.style.top = (pos.Y - this.circleRadius) + "px";
                    }
                    else if(unit.zone_id == -3) //Draft
                    {
                        if(unit.type != 57)
                        {
                            var categ = unit.category;
                            if(unit.category == "TITAN")
                            {
                                categ = "GOD";
                            }
                            dojo.place(this.format_block('jstpl_unittoken', unit),$(categ+'draft'));
                        }
                    }
                    else {
                        var side = unit.zone_id == -1?'bottom':'top';
                        if(!this.isSpectator && this.players[this.player_id].player_no == 2)
                        {
                            side = unit.zone_id == -2?'bottom':'top';
                        }
                        dojo.place(this.format_block('jstpl_unittoken', unit),$('side'+side));
                    }
                }
                
        		dojo.place(this.format_block('jstpl_dashboard', unit),$('phdashboard'));
                unit.powers.forEach(function(power) {
                    dojo.place(this.format_block('jstpl_power', power),$('powers'+unit_id));
                }.bind(this));
                

                var talentshtml = '';
                for (let key in unit.talents) { 
                    var talent = unit.talents[key];
                    talent.name = _(talent.name);       
                    talent.description = _(talent.description);  
                    if(!talent.trait)
                    {                  
                        talentshtml += this.format_block('jstpl_talentdesc', talent);
                        dojo.place('<div>'+_(talent.name).toUpperCase()+'</div>',$('dashboard_talents'+unit_id));
                    }
                    else
                    {
                        this.addTooltipHtml( 'dashboardtrait'+unit_id, this.format_block('jstpl_talentdesc', talent),1000);
                    }
                }
                

     		    this.addTooltipHtml( 'dashboard_talents'+unit_id, talentshtml,1000);

                 var statushtml = '';
                 for (let key in unit.status) {  
                     if(this.status.hasOwnProperty(unit.status[key]))  
                     {             
                         statushtml += "- "+_(this.status[unit.status[key]])+"<br/>";
                     }
                 };
                 if(statushtml == '')
                 {
                     statushtml = _("Nothing special");
                 }
                 this.addTooltipHtml( 'dashboardhelp'+unit_id, statushtml,1000);

                 if(unit.attachment.id>0)
                 {
                    unit.attachment['posx'] = 10*(unit.attachment.id-1);
                    dojo.place(this.format_block('jstpl_attachment', unit.attachment), $('dashboard'+unit.id));
                 }
            }

            this.units = gamedatas.units;
            this.units[0] = {"name":_('aow card')};
            this.units[-1] = {"name":_('rune card')};

            for(var card_id in gamedatas.hand)
            {
                var card = gamedatas.hand[card_id];
                card['name'] = _(this.cardTypes[card['card_type']]["name"]);
                card['category'] = this.cardTypes[card['card_type']]["category"];
                card['imgfull'] = g_gamethemeurl+"img/unit"+card.card_type+".png";
        		dojo.place(this.format_block('jstpl_card', card),$('hand'));
            }   
            
            for(var die_id in gamedatas.dices)
            {
                var die = gamedatas.dices[die_id];
        		dojo.place(this.format_block('jstpl_die', die),$('dices'));   
            } 

            for(var token_id in gamedatas.tokens)
            {
                var token = gamedatas.tokens[token_id];
                console.log(token);
                if(token.location.startsWith("zone"))
                {
                    const pos = this.getAvailablePosition(token.location);
                    dojo.place(this.format_block('jstpl_token', token),$("board")); 
                    var unithtml =  document.getElementById("token"+token.id);
                    unithtml.style.left = (pos.X - this.circleRadius) + "px";
                    unithtml.style.top = (pos.Y - this.circleRadius) + "px";
                }
                else{
                    dojo.place(this.format_block('jstpl_token', token),$(token.location)); 
                }  
            }   
            
            for(var att_id in gamedatas.attachmentdrafts)
            {
                var args = { args : {attachment : null}};
                args.args.attachment = gamedatas.attachmentdrafts[att_id];
                this.notif_placeattachment(args);
            }

            for(var zone_id in gamedatas.zones)
            {
                var zone = gamedatas.zones[zone_id];
                zone.title = _(zone.title);
                zone.description = _(zone.description);
                this.addTooltipHtml( "zone"+zone.id, this.format_block('jstpl_zonedesc', zone),1000);
            }
            
            
            dojo.query('#phdashboard > *').addClass('hidden');

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            dojo.query("#zoom").connect('onclick', this, 'onZoom' );  
            if(this.debug == 1)
            {
                this.showZoneId();
            }
            this.adaptViewportSize();
            this.refreshHand();
            
        	dojo.query("[data-unitid]").connect('onclick', this, 'onView' );
            dojo.query("[data-unitid]").connect('onclick', this, 'onSelect' ); 
            dojo.query("path").connect('onclick', this, 'onSelect' ); 
            dojo.query(".upperdice").connect('onclick', this, 'onSelect' ); 
            dojo.query(".token").connect('onclick', this, 'onSelect' ); 
            dojo.query(".discard").connect('onclick', this, 'onShowDiscard' ); 
            dojo.query("#discardclose").connect('onclick', this, function(event) { document.getElementById('discardshow').classList.add('hidden')} ); 
            dojo.query("#draftclose").connect('onclick', this, function(event) { document.getElementById('draft').classList.toggle('hidden')} ); 
            dojo.query(".dashboardhelp").connect('onclick', this, function(event) { this.tooltips[event.currentTarget.id].open(event.currentTarget.id); }.bind(this) ); 
            
            this.addTooltipHtmlToClass( 'tokenstele', _('Stele'),1000);
            this.addTooltipHtmlToClass( 'tokentree', _('Tree'),1000);
            this.addTooltipHtmlToClass( 'tokenrune', _('<b>Rune :</b> Runes are the divine stones in Norse Mythology. Your divinity may absorb 4 of them to win the game.'),1000);
            this.addTooltipHtmlToClass( 'reg1', _('<b>OFFENSE:</b> the number of dice the unit rolls for its first assault in an attack.'),1000);
            this.addTooltipHtmlToClass( 'reg2', _('<b>DEFENSE:</b> a value that denotes how difficult it is to wound the unit.'),1000);
            this.addTooltipHtmlToClass( 'reg3', _('<b>RANGE:</b> the maximum distance in number of areas at which the unit may make an attack. Range 0 is the area the unit occupies.'),1000);
            this.addTooltipHtmlToClass( 'reg4', _('<b>MOVEMENT:</b> the number of areas the unit travels when they walk.'),1000);
            this.addTooltipHtmlToClass( 'reg5', _('<b>AVAILABLE POWER:</b> the number of dice used for offensive power attacks (number) or the availability of a power (symbol). If this entry is a dash, then that power can no longer be used.'),1000);
            this.addTooltipHtmlToClass( 'reg6', _('<b>VITALITY:</b> number of remaining vitality points.'),1000);

        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            document.querySelectorAll('.selectable').forEach(function(element) { element.classList.remove('selectable');});
            document.querySelectorAll('.selected').forEach(function(element) { element.classList.remove('selected');});
            document.getElementById('anytime').innerHTML = '';

            if(args.args != null && args.args.players != null && !this.isSpectator)
            {
                const butanyTime = args.args.players[this.player_id];
                for(var but_id in butanyTime)
                {
                    butanyTime[but_id]['id'] = but_id;
                    butanyTime[but_id]['title'] = _( butanyTime[but_id]['title']);
                    dojo.place(this.format_block('jstpl_but', butanyTime[but_id]),$("anytime"));                     
                    dojo.query("#"+but_id).connect('onclick', this, 'onSelectAnytime' ); 
                }
            }

            if(document.getElementById('draft') != null && document.querySelectorAll('#draft .unit').length>0) 
            {
                document.getElementById('draft').classList.remove('hidden');
                document.getElementById('draftclose').classList.remove('hidden');
            }
            else{
                document.querySelectorAll('path').forEach(function(element) { element.setAttribute('style', 'fill: none');});
                dojo.query("#draft").forEach(dojo.destroy); 
                dojo.query("#draftclose").forEach(dojo.destroy); 
                dojo.query(".rp").forEach(dojo.destroy); 
            }
            

            switch( stateName )
            {
            
                case 'playerTurn':
                    if( this.isCurrentPlayerActive() )
                    {

                        if(args.args.titleyou != null)
                        {
                            $('pagemaintitletext').innerHTML = this.format_string_recursive(_(args.args.titleyou).replace('${you}', this.divYou()), args.args);  
                        }
    
                        if(args.args.pickcards != null)
                        {
                            document.getElementById('discardcards').innerHTML = '';
                            document.getElementById('dtitle').innerHTML = _("Choose from");
                            for(var card_id in args.args.pickcards)
                            {
                                var card = args.args.pickcards[card_id];
                                card['nb'] = 1;
                                card['name'] = _(this.cardTypes[card['card_type']]["name"]);
                                card['category'] = this.cardTypes[card['card_type']]["category"];
                                card['imgfull'] = g_gamethemeurl+"img/unit"+card.card_type+".png";
                                dojo.place(this.format_block('jstpl_card', card),$("discardcards")); 
                            }
                            document.getElementById('discardshow').classList.remove('hidden');
                            dojo.query("#discardcards .card").connect('onclick', this, 'onSelect' ); 
                        }

                        if(args.args.selectable != null)
                        {
                            for( var sid in args.args.selectable )
                            {
                                if(!sid.startsWith("but"))
                                {
                                    const monPath = document.getElementById(sid);
                                    if(monPath != null)
                                    {
                                        monPath.classList.add('selectable');
                                        if(args.args.selectable[sid]['selected'])
                                        {
                                            monPath.classList.add('selected');
                                        }
                                    }
                                }
                            }
                        }
                        this.args = args.args;
                        this.selected = null;
                    }
                    else
                    {                        
                        if(args.args.title != null)
                        {
                            $('pagemaintitletext').innerHTML = this.format_string_recursive(_(args.args.title).replace('${actplayer}', this.divActPlayer()), args.args);  
                        }
                    }

                    if(args.args.status != null)
                    {
                        for( var unitid in args.args.status )
                        {
                            var statushtml = '';
                            for (let key in args.args.status[unitid]) {  
                                if(this.status.hasOwnProperty(args.args.status[unitid][key]))  
                                {             
                                    statushtml += "- "+_(this.status[args.args.status[unitid][key]])+"<br/>";
                                }
                            };
                            if(statushtml == '')
                            {
                                statushtml = _("Nothing special");
                            }
                            this.addTooltipHtml( 'dashboardhelp'+unitid, statushtml,1000);
                        }
                    }
                    
                    if(this.debug == 1)
                    {
                        dojo.style('sidetop', {height: "500px"});
    				    dojo.query("#pendings").forEach(dojo.destroy); 
                        dojo.place(this.genererTableau(args.args.pendings),$('sidetop'));
                    }
                    break;  
                    
                   case 'client_selectTarget':                 
                   dojo.query("#"+ this.selected).addClass("selected");
                    for( var sid in this.args.selectable[this.selected]["target"] )
                        {
                            const monPath = document.getElementById(this.args.selectable[this.selected]["target"][sid]);
                            if(monPath != null)
                            {
                                monPath.classList.add('selectable');
                            }
                        }
                    break;

                    
                    case 'client_confirm':
                        document.getElementById(this.selected).classList.add( "selected" );
                        break;
           
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            
            document.querySelectorAll('.selectable').forEach(function(element) { element.classList.remove('selectable');});
            switch( stateName )
            {
           
                case 'playerTurn':
                    if(this.args != null && this.args.pickcards != null)
                    {
                        document.getElementById('discardcards').innerHTML = '';
                        document.getElementById('discardshow').classList.add('hidden')
                    }
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
                    case 'playerTurn':
                        if(args.selectable != null)
                        {
                            for( var sid in args.selectable )
                            {
                                if(sid.startsWith("but"))
                                {
                                    this.addActionButton( sid, _(args.selectable[sid]["title"]) ,'onSelect', null, null, args.selectable[sid]["color"] );
                                }
                            }
                        }
                        break;

                    case 'client_confirm':
                    this.addActionButton( 'confirm', _("Confirm") ,'onConfirm' );
                    this.addActionButton( 'cancel', _("Cancel") ,'onCancel', null, null, 'gray' );
                        break;

                        
                    case 'client_selectTarget':
	                	this.addActionButton( 'cancel', _("Cancel") ,'onCancel', null, null, 'red' );
                        break;
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods

        onShowDiscard:function(event)
        {          
            dojo.stopEvent( event ); 
            var player_id = event.currentTarget.id.replace("discardshow","");
            var discard = this.players[player_id]['discardDetails'];
            document.getElementById('discardcards').innerHTML = '';
            document.getElementById('dtitle').innerHTML = this.replacePlayerName(_('${player_name}\'s discard').replace('${player_name}', this.players[player_id].player_name));
            for(var card_type in discard)
            {
                var nb = discard[card_type];

                var card = this.cardTypes[card_type];
                card['nb'] = nb;
                card['card_id'] = Math.random().toString().replace(".","");
                card['name'] = _(this.cardTypes[card['card_type']]["name"]);
                card['category'] = this.cardTypes[card['card_type']]["category"];
                card['imgfull'] = g_gamethemeurl+"img/unit"+card.card_type+".png";

                dojo.place(this.format_block('jstpl_discardcard', card),$("discardcards"));
                dojo.place(this.format_block('jstpl_card', card),$('discardtype'+card_type)); 
            }
            document.getElementById('discardshow').classList.remove('hidden');
        },

        onCloseDiscard:function(event)
        {          
            dojo.stopEvent( event ); 
            document.getElementById('discardshow').classList.add('hidden');
        },

        onSelectAnytime:function(event)
        {          
            dojo.stopEvent( event ); 
            this.ajaxcall( "/mythicbattlesragnarok/mythicbattlesragnarok/actSelect.html", { 
                lock: true,
                arg1: event.currentTarget.id
            }, 
            this, function( result ) {}, function( is_error) {} ); 
        },
        
        onSelect:function(event)
        {          
            dojo.stopEvent( event ); 
            if( !this.isCurrentPlayerActive()) 
             {   return; }


             //force selected change
             if(!event.currentTarget.classList.contains('selectable') && !event.currentTarget.id.startsWith('but'))
             {
                this.restoreServerGameState();  
                this.selected = null; 
                if( this.args.selectable[event.currentTarget.id] == null)
                {     
                    return;
                }
             }

             if( !(event.currentTarget.classList.contains('selectable') || event.currentTarget.id.startsWith('but')) )
             {   return; }

             if(this.args.multiple)
            {          
                if(event.currentTarget.id.startsWith('but'))
                {
                    var selected = '';
                    document.querySelectorAll('.selected').forEach(function(element) { 
                        selected += ' '+element.id;
                        element.classList.remove('selected');
                    });

                    document.querySelectorAll('.selectable').forEach(function(element) { element.classList.remove('selectable');});

                    this.ajaxcall( "/mythicbattlesragnarok/mythicbattlesragnarok/actSelect.html", { 
                        lock: true,
                        arg1: event.currentTarget.id,
                        arg2: selected
                    }, 
                    this, function( result ) {}, function( is_error) {} ); 
                }   
                else{
                    
                    if(document.querySelectorAll('.selected').length >= this.args.multiple && !event.currentTarget.classList.contains('selected'))
                    {
                        document.querySelector('.selected').classList.toggle("selected");
                    }
                    document.getElementById(event.currentTarget.id).classList.toggle("selected");  
                }                 
            }
            else
            {
                document.querySelectorAll('.selectable').forEach(function(element) { element.classList.remove('selectable');});
                if(this.selected != null)
                {
                    this.ajaxcall( "/mythicbattlesragnarok/mythicbattlesragnarok/actSelect.html", { 
                        lock: true,
                        arg1: this.selected,
                        arg2: event.currentTarget.id
                    }, 
                    this, function( result ) {}, function( is_error) {} ); 
                }
                else if(this.args.selectable[event.currentTarget.id].confirm != null)
                {
                    this.selected = event.currentTarget.id;
                    var args = this.args.selectable[event.currentTarget.id];                
                    args['unitid_display'] = this.selected;

                        this.setClientState("client_confirm", {
                            descriptionmyturn: _(this.args.selectable[event.currentTarget.id].confirm),
                            args:  args
                        });
                }
                else if(this.selected == null && this.args.selectable[event.currentTarget.id].target != null)
                {
                    this.selected = event.currentTarget.id;
                    var title = this.args.selectable[event.currentTarget.id]['title'];
                    var args = this.args.selectable[event.currentTarget.id];  
                    this.setClientState("client_selectTarget", {
                        descriptionmyturn: _(title),
                        args: args
                    });
                }
                
                else{
                    this.ajaxcall( "/mythicbattlesragnarok/mythicbattlesragnarok/actSelect.html", { 
                        lock: true,
                        arg1: event.currentTarget.id
                    }, 
                    this, function( result ) {}, function( is_error) {} ); 
                }            
            }
        },

        onConfirm:function(event)
        {
            dojo.stopEvent( event );  
            if(this.isCurrentPlayerActive() && this.checkAction( "select" ) ) {
            		 
                document.querySelectorAll('.selectable').forEach(function(element) { element.classList.remove('selectable');});
                this.ajaxcall( "/mythicbattlesragnarok/mythicbattlesragnarok/actSelect.html", { 
                    lock: true,
                    arg1: this.selected
                }, 
                this, function( result ) {}, function( is_error) {} );             		 
            }
        },
        
        onCancel: function(evt)
        {
            this.restoreServerGameState();
        }, 

        onView:function(event)
        {
            dojo.stopEvent( event );  
			var unit_id = event.currentTarget.getAttribute('data-unitid');
            if(unit_id > 0)
            {
                dojo.query('#phdashboard > *').addClass('hidden');
                dojo.query('#dashboard'+unit_id).removeClass('hidden');
            }
        },

        onViewAttach:function(event)
        {
            dojo.stopEvent( event );  
			var unit_id = event.currentTarget.id.replace("miniattachment","");
            if(unit_id > 0)
            {
                dojo.query('#phdashboard > *').addClass('hidden');
                dojo.query('#attachment'+unit_id).removeClass('hidden');
            }
        },

        divYou : function() {
            var color = this.players[this.player_id].player_color;
            var color_bg = "";
            var you = "<span style=\"font-weight:bold;color:#" + color + ";" + color_bg + "\">" + _("You") + "</span>";
            return you;
        },

        replacePlayerName : function(log) {

        	for(var key in this.players)
        	{
        		var player = this.players[key];
        		var color = player.color;
                var name = player.name;
                var color_bg = "";
                log = log.replace(name, "<span style=\"font-weight:bold;color:#" + color + ";" + color_bg + "\">" + name + "</span>");
        	}
            
            return log;
        },
        divActPlayer : function() {        	
            var color = this.players[this.getActivePlayerId()].color;
            var name = this.players[this.getActivePlayerId()].name;
            var color_bg = "";
            var you = "<span style=\"font-weight:bold;color:#" + color + ";" + color_bg + "\">" + name + "</span>";
            return you;
        },
        
        format_string_recursive : function(log, args) {
            try {
                if (log && args && !args.processed) {
                    args.processed = true;
                    if(args['unitid_display'] != null)
                    {             
                        unitid = args['unitid_display']; 
                        if (isNaN(unitid)){
                            unitid = document.getElementById(args['unitid_display']).getAttribute('data-unitid');
                        }
                        var unit = this.units[unitid];
                        if(unitid<=0 || unit.player_id == 0 )
                        {
                            args['unitid_display'] = _(unit.name);
                        }
                        else{
                            args['unitid_display'] = '<span style="color:#'+this.players[unit.player_id].player_color+'">'+_(unit.name)+'</span>';
                        }
                    }
                    if(args['unitid_display2'] != null)
                    {             
                        unitid = args['unitid_display2']; 
                        if (isNaN(unitid)){
                            unitid = document.getElementById(args['unitid_display2']).getAttribute('data-unitid');
                        }
                        var unit = this.units[unitid];
                        if(unitid<=0 || unit.player_id == 0 )
                        {
                            args['unitid_display2'] = _(unit.name);
                        }
                        else{
                            args['unitid_display2'] = '<span style="color:#'+this.players[unit.player_id].player_color+'">'+_(unit.name)+'</span>';
                        }
                    }
                    const stats = ['offense', 'defense','movement','vitality', 'range'];
                    stats.forEach(stat =>
                    {
                        if(args[stat] != null)
                        { 
                            if(isNaN(args[stat]))
                            {
                                var resume = "";                            
                                for (let key in args[stat]) { 
                                    if(key != 'total')
                                    {
                                        
                                        resume += (args[stat][key]>=0?" +":" ")+args[stat][key]+" ("+_(key)+")";
                                    }
                                }
                                resume = resume.substring(2);
                                args[stat] = args[stat]['total']+' <div title="'+resume+'" class="mbr_'+stat+'"></div>';
    
                            }
                            else{
                                args[stat] = args[stat]+' <div class="mbr_'+stat+'"></div>';
                            }
                        }
                    });
                }
            } catch (e) {
                console.error(log,args,"Exception thrown", e.stack);
            }
            return this.inherited(arguments);
        },
      		
 isCircleInsideSVGPath: function(path, cx, cy, radius) {
    // Obtenez les données du chemin SVG
  
    // Créez un élément SVG temporaire pour le cercle
    const svgns = "http://www.w3.org/2000/svg";
    const svg = document.createElementNS(svgns, "svg");
    const circle = document.createElementNS(svgns, "circle");
    circle.setAttribute("cx", cx);
    circle.setAttribute("cy", cy);
    circle.setAttribute("r", radius);
  
    // Ajoutez le cercle au SVG temporaire
    svg.appendChild(circle);
  
    // Obtenez un point SVG pour les coordonnées du cercle
    const point = svg.createSVGPoint();
    point.x = cx;
    point.y = cy;
  
    // Utilisez la méthode `isPointInFill` pour détecter une intersection
    let isInside = true;
    point.x = cx - radius;
    point.y = cy;
    isInside = isInside && path.isPointInFill(point);
    point.x = cx + radius;
    point.y = cy;
    isInside = isInside && path.isPointInFill(point);
    point.x = cx;
    point.y = cy + radius;
    isInside = isInside && path.isPointInFill(point);
    point.x = cx;
    point.y = cy - radius;
    isInside = isInside && path.isPointInFill(point);
  
    // Retirez le cercle temporaire de l'élément SVG
    svg.removeChild(circle);
    return isInside;
  },
  
   distanceEntrePoints: function(x1, y1, x2, y2) {
    // Calcul de la distance euclidienne entre deux points
    return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
  },
  
  estTropProche: function(x, y, autresCercles, rayon) {
    for (const cercle of autresCercles) {
      const autreX = parseInt(cercle.style.left)  + rayon;
      const autreY = parseInt(cercle.style.top)  + rayon;
      const distance = this.distanceEntrePoints(x, y, autreX, autreY);
      
      // Comparer la distance avec la distance minimale
      if (distance < rayon * 2 ) {
        return true; // Le point est trop proche d'au moins un autre cercle
      }
    }
  
    return false; // Le point est à une distance suffisante de tous les autres cercles
  },
  
  getZone: function(x,y)
  {
      const point = svg.createSVGPoint();
        point.x = x;
        point.y = y;
      for (const zone of document.querySelectorAll("path")) {
          if(zone.isPointInFill(point))
          {
              return zone.id;
          }
      }
      return null;
  },
  
  zonesBetween: function(center1, center2, zone1, zone2)
  {
      var list = [];
      for (let i = 1; i <= 100; i++) {
          var zoneN = getZone(center1.x + (center2.x-center1.x)*i/100,center1.y + (center2.y-center1.y)*i/100);
          if(zoneN != null && zoneN != zone1 && zoneN != zone2 && !list.includes(zoneN.replace("zone","")))
          {
              list.push(zoneN.replace("zone",""));
          }
      }
      return list;
  },
  
  hasBoundary: function(zone1, zone2) {
      
      const path1 = document.getElementById(zone1);
      const path2 = document.getElementById(zone2);
      var boundingRect1 = path1.getBBox();
      var boundingRect2 = path2.getBBox();
      
      
      for (let i = 1; i <= 100; i++) {
      
          let point1, point2, tries = 0;
          point1 = svg.createSVGPoint();
          point2 = svg.createSVGPoint();
          point1.x = Math.random() * boundingRect1.width + boundingRect1.x ;
          point1.y = Math.random() * boundingRect1.height + boundingRect1.y;
          point2.x = Math.random() * boundingRect2.width + boundingRect2.x ;
          point2.y = Math.random() * boundingRect2.height + boundingRect2.y;
          if(path1.isPointInFill(point1) && path2.isPointInFill(point2))
          {
              var bt = this.zonesBetween(point1,point2, zone1, zone2);
              if(bt.length == 0)
              {
                  return true;
              }
          }
      }
      return false;
  },
  
  getAvailablePosition: function(zoneId)
  {
      const path = document.getElementById(zoneId);
      const cercles = document.querySelectorAll(".unit, .token"); // Sélectionnez tous les cercles
      var boundingRect = path.getBBox();
      let randomX, randomY, tries = 0;
      do {
        randomX = Math.random() * (boundingRect.width - this.circleRadius*2) + boundingRect.x + this.circleRadius;
        randomY = Math.random() * (boundingRect.height - this.circleRadius*2) + boundingRect.y + this.circleRadius;
          tries++;
      } while (!(this.isCircleInsideSVGPath(path, randomX, randomY, this.circleRadius) && !this.estTropProche(randomX, randomY, cercles, this.circleRadius * 1.2)) && tries < 100);
     
      return {X:randomX, Y: randomY};
  },

  refreshHand:function()
        {
            var zoneid = "hand";

            let divs = document.querySelectorAll('#hand .card_name');
            let divsArray = Array.from(divs);
        
            divsArray.sort(function (a, b) {
                let textA = a.textContent.trim().toLowerCase();
                let textB = b.textContent.trim().toLowerCase();
                return textA.localeCompare(textB);
            });
        
            let container = document.getElementById('hand');
            divsArray.forEach(function (div) {
                container.appendChild(div.parentNode);
            });

            var width = dojo.marginBox(zoneid).w;
        	var cardWidth = this.cardWidth;
        	
        	var totCards = dojo.query('#'+zoneid+' .card').length;
        	if(totCards != 0)
        	{
	        	var spaceLeft = width - totCards * this.cardWidth;
	        	if(spaceLeft>0)
	        	{
		        	var margin = spaceLeft / (totCards+1);	
		        	var spacing = 10;
		        	if(margin > this.cardWidth/4)
		        	{
		        		margin = this.cardWidth/4;
		        		spacing = (width-((totCards+0.5) * (margin+this.cardWidth)))/2;
		        	}
		        	var index = 0;
		        	dojo.query('#'+zoneid+' .card').forEach(function(selectTag){

		        		var leftx = (spacing + margin + index * ( cardWidth + margin));
                        
		        		dojo.style(selectTag.id, {
		        			'z-index': index,
			                 left: leftx + "px",
			                 top: "0px",
			        		transition: "0.5s"
			             });
		        		index++;
		        	});
	        	}
	        	else
	        	{
	        		var margin = spaceLeft / (totCards-1);			
		        	var index = 0;
		        	dojo.query('#'+zoneid+' .card').forEach(function(selectTag){
		        		
		        		var leftx = (index * (cardWidth + margin));
		        		dojo.style(selectTag.id, {
		        			'z-index': index,
			                 left: leftx + "px",
			                 top: "0px",
			        			transition: "0.5s"
			             });
		        		index++;
		        	});
	        	}
        	}
        	
        },
  
        showZoneId: function() {
            const svgns = "http://www.w3.org/2000/svg";
            const svg = document.createElementNS(svgns, "svg");
            var corr = [];
            
            for (const zone of document.querySelectorAll("path")) {
                    
                for (const center of document.querySelectorAll("rect")) {
                    const point = svg.createSVGPoint();
                        point.x = parseFloat(center.getAttribute('x')) + parseFloat(center.getAttribute('width'))/2;
                        point.y = parseFloat(center.getAttribute('y')) + parseFloat(center.getAttribute('height'))/2;
                    if(zone.isPointInFill(point))
                    {
                        corr[zone.id] = point;
                        continue;
                    }
                }
            }
            
            for (let [zone1, center1] of Object.entries(corr)) {
            const circleDiv = document.createElement("div");
                    circleDiv.className = "zonecenter";
                    circleDiv.style.left = center1.x + "px";
                    circleDiv.style.top = center1.y + "px";
                    circleDiv.textContent = zone1.replace("zone","");
                    document.getElementById("board").appendChild(circleDiv);
            }
        },

        genererTableau: function (objets) {
            // Créez l'en-tête du tableau avec les noms de propriétés
            let tableauHtml = '<table id="pendings" border="1"><tr>';
           
          var first = true;
            // Ajoutez les lignes avec les valeurs des propriétés
            for( var id in objets )
             {                
                var objet = objets[id];
                if(first) {
                    for (let propriete in objet) {
                        tableauHtml += '<th>' + propriete + '</th>';
                      }
                      tableauHtml += '</tr>';
                      first = false;
                }

                tableauHtml += '<tr>';
                for (let propriete in objet) {
                    tableauHtml += '<td>' + objet[propriete] + '</td>';
                }
                tableauHtml += '</tr>';

            };
          
            tableauHtml += '</table>';
            return tableauHtml;
          },

        ///////////////////////////////////////////////////
        //// Player's action
        
            onZoom : function( evt )
            {     
                evt.preventDefault();
                this.zoomIndex = (this.zoomIndex+1)%(this.zoomScales.length);
                this.adaptViewportSize();
                
            },

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        setupNotifications: function()
        {            
            // Example 1: standard notification handling
             dojo.subscribe( 'discard', this, "notif_discard" );
             dojo.subscribe( 'move', this, "notif_move" );
             dojo.subscribe( 'dices', this, "notif_dices" );
             this.notifqueue.setSynchronous( 'dices', 2000 );
             dojo.subscribe( 'fadeOutAndDestroy', this, "notif_fadeOutAndDestroy" );
             dojo.subscribe( 'setDice', this, "notif_setDice" );
             dojo.subscribe( 'wounds', this, "notif_wounds" );
             dojo.subscribe( 'draw', this, "notif_draw" );
             dojo.subscribe( 'movetoNewParent', this, "notif_movetoNewParent" );
             dojo.subscribe( 'absorb', this, "notif_absorb" );
             dojo.subscribe( 'backontable', this, "notif_backontable" );
             dojo.subscribe( 'drop', this, "notif_drop" );
             dojo.subscribe( 'updatecounter', this, "notif_updatecounter" );
             dojo.subscribe( 'innerhtml', this, "notif_innerhtml" );
             dojo.subscribe( 'placeattachment', this, "notif_placeattachment" );
            },  
    
            

        notif_backontable: function( notif )
        {
            var unit = notif.args.unit;
            unit['color'] = this.players[unit.player_id].player_color;
            unit['imgfull'] = g_gamethemeurl+"img/unit"+unit.type+".png";
            unit['category'] = notif.args.category;
            var side = unit.zone_id == -1?'bottom':'top';
            if(!this.isSpectator && this.players[this.player_id].player_no == 2)
            {
                side = unit.zone_id == -2?'bottom':'top';
            }
            dojo.place(this.format_block('jstpl_unittoken', unit),$('side'+side));            
            dojo.query("#unit"+unit.id).connect('onclick', this, 'onView' ); 
            dojo.query("#unit"+unit.id).connect('onclick', this, 'onSelect' );
        },

        notif_drop: function( notif )
        { 
            var token = notif.args.token;
            var mobile = document.getElementById("token"+token.id);

            if(mobile == null)
            {
                dojo.place(this.format_block('jstpl_token', notif.args.token),$('board')); 
                dojo.query("#token"+token.id).connect('onclick', this, 'onSelect' ); 
                mobile = document.getElementById("token"+token.id);
            }

            const pos = this.getAvailablePosition(token.location);
            mobile.style.left = (pos.X - this.circleRadius) + "px";
            mobile.style.top = (pos.Y - this.circleRadius) + "px";
            dojo.place(mobile, 'board');
            mobile.offsetTop;//force re-flow
        },

        notif_absorb: function( notif )
        { 
            const existing = document.getElementById("token"+notif.args.token.id);
            if(existing != null)
            {
                existing.id += Math.random().toString().replace(".","");
                this.fadeOutAndDestroy( existing.id,2000);
            }
            dojo.place(this.format_block('jstpl_token', notif.args.token),$(notif.args.token.location)); 

            dojo.query("#token"+notif.args.token.id).connect('onclick', this, 'onSelect' ); 
        },
        
        notif_movetoNewParent: function( notif )
        { 
            const mobile = document.getElementById(notif.args.mobile_obj);
            const target = document.getElementById(notif.args.target_obj);
            mobile.style.left = (mobile.offsetLeft - target.offsetLeft) + "px";
            mobile.style.top = (mobile.offsetTop - target.offsetTop) + "px";
            dojo.place(mobile, target);
            mobile.offsetTop;//force re-flow
            mobile.style.left = "0px";
            mobile.style.top = "0px";
        },

        notif_move: function( notif )
        {             
            var unit = notif.args.unit;
            unit['color'] = this.players[unit.player_id].player_color;
            unit['imgfull'] = g_gamethemeurl+"img/unit"+unit.type+".png";
            unit['category'] = notif.args.category;
            var existing = document.getElementById("unit"+unit.id);
            if(existing == null || existing.parentElement.id != "board")
            {
                //deployment
                if(existing != null)
                {
                    existing.id += Math.random().toString().replace(".","");
                    this.fadeOutAndDestroy( existing.id,2000);
                }
                dojo.place(this.format_block('jstpl_unittoken', unit),$('board'));
                existing = document.getElementById("unit"+unit.id);
                dojo.query("#unit"+unit.id).connect('onclick', this, 'onView' ); 
                dojo.query("#unit"+unit.id).connect('onclick', this, 'onSelect' ); 
            }

            const pos = this.getAvailablePosition("zone"+unit.zone_id);
            existing.style.left = (pos.X - this.circleRadius) + "px";
            existing.style.top = (pos.Y - this.circleRadius) + "px";
        },
        
        notif_discard: function( notif )
        {            
            var card = document.getElementById("card"+notif.args.card.card_id);
            if(card != null)
            {
                this.fadeOutAndDestroy( "card"+notif.args.card.card_id,1000);
                setTimeout(function() {
                    this.refreshHand();
                }.bind(this),1500);
            }
            else{
                var card = notif.args.card;
                card['name'] = _(this.cardTypes[card['card_type']]["name"]);
                card['category'] = this.cardTypes[card['card_type']]["category"];
                card['imgfull'] = g_gamethemeurl+"img/unit"+card.card_type+".png";
                card['card_id'] += 'tmp' + Math.random() * 10;
        		dojo.place(this.format_block('jstpl_card', card),$('frontboard'));
                setTimeout(function() {
                    this.fadeOutAndDestroy( "card"+card['card_id'],1000);
                }.bind(this),2000);
            }
        },  

        notif_dices: function( notif )
        {            
            dojo.query(".upperdice").forEach(dojo.destroy);
            for(var die_id in notif.args.dices)
            {
                var die = notif.args.dices[die_id];
        		dojo.place(this.format_block('jstpl_die', die),$('dices'));
            } 
            dojo.query(".upperdice").connect('onclick', this, 'onSelect' ); 
        },

        notif_setDice: function( notif )
        { 
            var die = notif.args.die;            
            var elDice = document.getElementById("dice" + die.id).parentElement;
            elDice.classList.remove( "sideup1", "sideup2", "sideup3", "sideup4", "sideup5", "sideup0" );
            elDice.classList.add( "sideup" + die.face );

            elDice = document.getElementById("diebadge" + die.id);
            elDice.firstElementChild.innerHTML = die.value;
        },

        notif_fadeOutAndDestroy: function( notif )
        {
            var existing = document.getElementById(notif.args.id);
            if(existing != null)
            {
                existing.id += Math.random().toString().replace(".","");
                this.fadeOutAndDestroy( existing.id,2000);
            }
        },

        notif_wounds: function( notif )
        {
            var unit = notif.args.unit;
                   
            document.getElementById("unithp"+unit.id).innerHTML = unit.hp;
            document.getElementById("unithpdash"+unit.id).innerHTML = unit.hp;

            var top = this.reglettes[notif.args.maxhp] + (notif.args.maxhp - unit['hp']) * 25.25;            
            document.getElementById("reglette"+unit.id).style.top = top + "px";
            
            if(notif.args.diff != 0)
            {
                var animId = ('wound' + Math.random()).replace(".","");
	            if(notif.args.diff>0)
	            {
	            	notif.args.diff = '+'+notif.args.diff;
	            }
	            
	            dojo.place(this.format_block('jstpl_wounds', { id:animId, 'wounds' : notif.args.diff, 'positive' : notif.args.diff>0?1:0}), $('unit'+unit.id));
                setTimeout(function() {
    				dojo.query("#"+animId).forEach(dojo.destroy); 
                }.bind(animId),3000); 
            }
        },

        notif_updatecounter: function( notif )
        {
            var player_id = notif.args.player_id;
            $('deck'+player_id).innerHTML = notif.args.deck;
            $('hand'+player_id).innerHTML = notif.args.hand;
            $('discard'+player_id).innerHTML = notif.args.discard;
            this.players[player_id]['discardDetails'] = notif.args.discardDetails
        },
        
        notif_draw: function( notif )
        {            
            var card = notif.args.card;
            if(card != null && this.cardTypes[card['card_type']] != null)
            {
                dojo.query("#card"+card['card_id']).forEach(dojo.destroy);
                card['name'] = _(this.cardTypes[card['card_type']]["name"]);
                card['category'] = this.cardTypes[card['card_type']]["category"];
                card['imgfull'] = g_gamethemeurl+"img/unit"+card.card_type+".png";
                dojo.place(this.format_block('jstpl_card', card),$('hand'));
                dojo.query("#card"+card['card_id']).connect('onclick', this, 'onView' );
                dojo.query("#card"+card['card_id']).connect('onclick', this, 'onSelect' ); 
            }
            this.refreshHand();
        },


        notif_innerhtml: function( notif )
        {   
            $(notif.args.id).innerHTML = notif.args.html;
        },

        notif_placeattachment : function (notif)
        {
            var attach =notif.args.attachment;   
            if(attach.unitid != null)
            {
                attach['posx'] = 10*(attach.id-1);
                dojo.place(this.format_block('jstpl_attachment', attach), $('dashboard'+attach.unitid));
            }  
            else{
                attach['posx'] = 2+9.5*(attach.id-1);
                if(attach.player_id != null)
                {
                    var side = attach.player_id == this.player_id?'bottom':'top';
                    dojo.place(this.format_block('jstpl_miniattachment', attach), $('side'+side));
                }
                else{
                    dojo.place(this.format_block('jstpl_miniattachment', attach), $('attachdraft'));
                }               
                attach['posx'] = 10*(attach.id-1);
                dojo.place(this.format_block('jstpl_attachment', attach), $('phdashboard'));
                
            dojo.query("#miniattachment"+attach.id).connect('onclick', this, 'onSelect' );
            dojo.query("#miniattachment"+attach.id).connect('onclick', this, 'onViewAttach' ); 
            }           
        }

   });             
});
