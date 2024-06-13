{OVERALL_GAME_HEADER}

<div class="flexa">
    <div id="fixboard">
        <div id="gboard">
            <div id="board">
                {BOARD_SVG}
            </div>
        </div>
        <div id="frontboard"></div>
        <div id="dices"></div>
        <div id="discardshow" class="hidden whiteonboard">
            <div id="discardclose" class=""><i class="fa fa-close" aria-hidden="true"></i></div>
            <div id="dtitle">Discard</div>
            <div id="discardcards"></div>
        </div>
        <div id="draft" class="hidden whiteonboard">
            <div id="textecategorygod" class="textecategory">DIVINITY</div>
            <div id="GODdraft" class="catDraft"></div>
            <div id="textecategoryhero" class="textecategory">HERO</div>
            <div id="HEROdraft" class="catDraft"></div>
            <div id="textecategorymonster" class="textecategory">MONSTER</div>
            <div id="MONSTERdraft" class="catDraft"></div>
            <div id="textecategorytroop" class="textecategory">TROOP</div>
            <div id="TROOPdraft" class="catDraft"></div>
            <div id="textecategoryattach" class="textecategory">ATTACHMENT</div>
            <div id="attachdraft" class="catDraft"></div>
        </div>
        <div id="draftclose" class=""><i class="fa fa-eye" aria-hidden="true"></i></div>
    </div>
    <div class="rightcol">    
        <div class="side" id="sidetop"> 
        </div>
        <div id="phdashboard"></div>
        <div class="side" id="anytime"></div>
        <div class="cards" id="hand"></div>
        <div class="side" id="sidebottom">
        </div>
    </div>
    <div id="zoom" class=""><i class="fa fa-search-plus" aria-hidden="true"></i></div>
</div>

<script type="text/javascript">

// Javascript HTML templates

var jstpl_token = '<div id="token${id}" class="token token${type}"></div>';
var jstpl_wounds = '<div id="${id}" class="diffwound pos${positive}" >${wounds}<div class="mbr_wounds pos${positive}"></div></div>';
var jstpl_score='<div class="mbr_score"><div id="deck${id}">${deck}</div><div class="mbr_icon backcard"></div><div id="hand${id}">${hand}</div><div class="mbr_icon hand"></div><div id="discard${id}">${discard}</div><div id="discardshow${id}" class="mbr_icon discard"></div></div>';
var jstpl_unittoken='<div class="unit unitcat${category}" data-unitid="${id}" id="unit${id}" style=" top:0px; left:0px; background-image: url(${imgfull}) !important; border-color: #${color};"><div class="heart-shape" style="background-color:#${color}"></div><div class="tokenhp" id="unithp${id}">${hp}</div></div>';
var jstpl_dashboard='<div class="dashboard cat${category} prevent-selection dashboardtype${type}" data-unitid="${id}" id="dashboard${id}" style="background-image: url(${imgfull}) !important;">\
                <div id="dashboardhelp${id}" class="dashboardhelp"><i class="fa fa-question" aria-hidden="true"></i></div>\
                <div id="dashboardtrait${id}" class="dashboard_trait ${traitname}"></div>\
                <div class="dashboard_name">${name}</div>\
                <div class="dashboard_cost">${cost}</div>\
                <div class="dashboard_talents" id="dashboard_talents${id}"></div>\
                <div class="activations limit${activation} limitspan${nbmeeple}">\
                    <div class="activation"></div>\
                    <div class="activation"></div>\
                    <div class="activation"></div>\
                    <div class="activation"></div>\
                    <div class="activation"></div>\
                    <div class="activation"></div>\
                    <span class="meeple"></span>\
                    <span class="meeple"></span>\
                    <span class="meeple"></span>\
                    <span class="meeple"></span>\
                    <span class="meeple"></span>\
                    <span class="meeple"></span>\
                </div>\
                <div class="aows limit${aow}">\
                    <div class="aow"></div>\
                    <div class="aow"></div>\
                    <div class="aow"></div>\
                    <div class="aow"></div>\
                    <div class="aow"></div>\
                    <div class="aow"></div>\
                </div>\
                <div class="powers" id="powers${id}">\
                </div>\
                <div class="reglette" id="reglette${id}" style="top:${top}px"><div id="regx1${id}" class="regitem reg1"></div><div id="regx2${id}" class="regitem reg2"></div><div id="regx3${id}" class="regitem reg3"></div><div id="regx4${id}" class="regitem reg4"></div><div id="regx5${id}" class="regitem reg5"></div><div id="regx6${id}" class="regitem reg6"></div> </div>\
                <div class="trooponly"><div class="heart-shape" style="background-color:#ff0000"></div><div class="tokenhp" id="unithpdash${id}">${hp}</div><div id="regt1${id}" class="regitem reg1"></div><div id="regt2${id}" class="regitem reg2"></div><div id="regt3${id}" class="regitem reg3"></div><div id="regt4${id}" class="regitem reg4"></div></div>\
            </div>';
var jstpl_power = '<div class="power powcolor${white} pow${type}">\
                        <div class="upperpower">\
                            <div class="uppericon">\
                                <div class="power_icon"></div>\
                                <div class="power_frame"></div>\
                            </div>\
                            <div class="power_title">${title}</div>\
                            <div class="power_aows limit${aow} limitspan${token}">\
                                <div class="aow"></div>\
                                <div class="aow"></div>\
                                <div class="aow"></div>\
                                <div class="aow"></div>\
                                <div class="aow"></div>\
                                <div class="aow"></div>\
                                <span class="powtoken"></span>\
                                <span class="powtoken"></span>\
                                <span class="powtoken"></span>\
                                <span class="powtoken"></span>\
                                <span class="powtoken"></span>\
                                <span class="powtoken"></span>\
                            </div>\
                        </div>\
                        <div class="power_description">${description}</div>\
                    </div>';
var jstpl_card = '<div class="card cat${category} cardtype${card_type}" data-unitid="${card_type_arg}" id="card${card_id}" style="background-image: url(${imgfull}) !important;"><div class="card_name center">${name}</div><div class="card_name left">${name}</div><div class="card_name right">${name}</div></div>';
var jstpl_discardcard = '<div class="phdiscardcard"><div id="discardtype${card_type}"></div>X${nb}</div>';
var jstpl_talentdesc = '<div class="talent"><b>${name} :</b> ${description}</div>';
var jstpl_zonedesc = '<div><b>${title} (${capacity}):</b> ${description}</div>';
var jstpl_die = '\
        <div class="upperdice" id="diceres${id}">\
            <div class="dice-result sideup${face} ">\
                <div class="dice" id="dice${id}">\
                    <div class="die-side side1 die-lining" data-numside="1"><div class="bside side-1"></div> </div>\
                    <div class="die-side side2 die-lining" data-numside="2"><div class="bside side-2"></div> </div>\
                    <div class="die-side side3 die-lining" data-numside="3"><div class="bside side-3"></div> </div>\
                    <div class="die-side side4 die-lining" data-numside="4"><div class="bside side-4"></div> </div>\
                    <div class="die-side side5 die-lining" data-numside="5"><div class="bside side-5"></div> </div>\
                    <div class="die-side side0 die-lining" data-numside="0"><div class="bside side-0"></div> </div>\
                </div>\
            </div>\
            <div class="diebadge" id="diebadge${id}"><div class="innerdiebadge">${value}</div></div>\
        </div>';
var jstpl_but = '<a href="#" class="action-button bgabutton bgabutton_gray" onclick="return false;" id="${id}">${title}</a>';
var jstpl_attachment = '<div id="attachment${id}" class="attachment" style="background-position-x:${posx}%"><div class="atttitle">${title}</div><div class="attdescription">${description}</div></div>';
var jstpl_miniattachment = '<div id="miniattachment${id}" class="miniattachment" style="background-position-x:${posx}%"></div>';

</script>  

{OVERALL_GAME_FOOTER}
