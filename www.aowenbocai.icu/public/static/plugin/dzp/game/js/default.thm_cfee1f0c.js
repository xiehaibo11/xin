window.skins={};
function __extends(d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
        function __() {
            this.constructor = d;
        }
    __.prototype = b.prototype;
    d.prototype = new __();
};
window.generateEUI = {};
generateEUI.paths = {};
generateEUI.styles = undefined;
generateEUI.skins = {"eui.Button":"resource/eui_skins/ButtonSkin.exml","eui.CheckBox":"resource/eui_skins/CheckBoxSkin.exml","eui.HScrollBar":"resource/eui_skins/HScrollBarSkin.exml","eui.HSlider":"resource/eui_skins/HSliderSkin.exml","eui.Panel":"resource/eui_skins/PanelSkin.exml","eui.TextInput":"resource/eui_skins/TextInputSkin.exml","eui.ProgressBar":"resource/eui_skins/ProgressBarSkin.exml","eui.RadioButton":"resource/eui_skins/RadioButtonSkin.exml","eui.Scroller":"resource/eui_skins/ScrollerSkin.exml","eui.ToggleSwitch":"resource/eui_skins/ToggleSwitchSkin.exml","eui.VScrollBar":"resource/eui_skins/VScrollBarSkin.exml","eui.VSlider":"resource/eui_skins/VSliderSkin.exml","eui.ItemRenderer":"resource/eui_skins/ItemRendererSkin.exml"}
generateEUI.paths['resource/eui_skins/ButtonSkin.exml'] = window.skins.ButtonSkin = (function (_super) {
	__extends(ButtonSkin, _super);
	function ButtonSkin() {
		_super.call(this);
		this.skinParts = ["labelDisplay","iconDisplay"];
		
		this.minHeight = 50;
		this.minWidth = 100;
		this.elementsContent = [this._Image1_i(),this.labelDisplay_i(),this.iconDisplay_i()];
		this.states = [
			new eui.State ("up",
				[
				])
			,
			new eui.State ("down",
				[
					new eui.SetProperty("_Image1","source","button_down_png")
				])
			,
			new eui.State ("disabled",
				[
					new eui.SetProperty("_Image1","alpha",0.5)
				])
		];
	}
	var _proto = ButtonSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		this._Image1 = t;
		t.percentHeight = 100;
		t.scale9Grid = new egret.Rectangle(1,3,8,8);
		t.source = "button_up_png";
		t.percentWidth = 100;
		return t;
	};
	_proto.labelDisplay_i = function () {
		var t = new eui.Label();
		this.labelDisplay = t;
		t.bottom = 8;
		t.left = 8;
		t.right = 8;
		t.size = 20;
		t.textAlign = "center";
		t.textColor = 0xFFFFFF;
		t.top = 8;
		t.verticalAlign = "middle";
		return t;
	};
	_proto.iconDisplay_i = function () {
		var t = new eui.Image();
		this.iconDisplay = t;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		return t;
	};
	return ButtonSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/CheckBoxSkin.exml'] = window.skins.CheckBoxSkin = (function (_super) {
	__extends(CheckBoxSkin, _super);
	function CheckBoxSkin() {
		_super.call(this);
		this.skinParts = ["labelDisplay"];
		
		this.elementsContent = [this._Group1_i()];
		this.states = [
			new eui.State ("up",
				[
				])
			,
			new eui.State ("down",
				[
					new eui.SetProperty("_Image1","alpha",0.7)
				])
			,
			new eui.State ("disabled",
				[
					new eui.SetProperty("_Image1","alpha",0.5)
				])
			,
			new eui.State ("upAndSelected",
				[
					new eui.SetProperty("_Image1","source","checkbox_select_up_png")
				])
			,
			new eui.State ("downAndSelected",
				[
					new eui.SetProperty("_Image1","source","checkbox_select_down_png")
				])
			,
			new eui.State ("disabledAndSelected",
				[
					new eui.SetProperty("_Image1","source","checkbox_select_disabled_png")
				])
		];
	}
	var _proto = CheckBoxSkin.prototype;

	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.percentHeight = 100;
		t.percentWidth = 100;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this._Image1_i(),this.labelDisplay_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		this._Image1 = t;
		t.alpha = 1;
		t.fillMode = "scale";
		t.source = "checkbox_unselect_png";
		return t;
	};
	_proto.labelDisplay_i = function () {
		var t = new eui.Label();
		this.labelDisplay = t;
		t.fontFamily = "Tahoma";
		t.size = 20;
		t.textAlign = "center";
		t.textColor = 0x707070;
		t.verticalAlign = "middle";
		return t;
	};
	return CheckBoxSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/HScrollBarSkin.exml'] = window.skins.HScrollBarSkin = (function (_super) {
	__extends(HScrollBarSkin, _super);
	function HScrollBarSkin() {
		_super.call(this);
		this.skinParts = ["thumb"];
		
		this.minHeight = 8;
		this.minWidth = 20;
		this.elementsContent = [this.thumb_i()];
	}
	var _proto = HScrollBarSkin.prototype;

	_proto.thumb_i = function () {
		var t = new eui.Image();
		this.thumb = t;
		t.height = 8;
		t.scale9Grid = new egret.Rectangle(3,3,2,2);
		t.source = "roundthumb_png";
		t.verticalCenter = 0;
		t.visible = false;
		t.width = 30;
		return t;
	};
	return HScrollBarSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/HSliderSkin.exml'] = window.skins.HSliderSkin = (function (_super) {
	__extends(HSliderSkin, _super);
	function HSliderSkin() {
		_super.call(this);
		this.skinParts = ["track","thumb"];
		
		this.minHeight = 8;
		this.minWidth = 20;
		this.elementsContent = [this.track_i(),this.thumb_i()];
	}
	var _proto = HSliderSkin.prototype;

	_proto.track_i = function () {
		var t = new eui.Image();
		this.track = t;
		t.height = 6;
		t.scale9Grid = new egret.Rectangle(1,1,4,4);
		t.source = "track_sb_png";
		t.verticalCenter = 0;
		t.percentWidth = 100;
		return t;
	};
	_proto.thumb_i = function () {
		var t = new eui.Image();
		this.thumb = t;
		t.source = "thumb_png";
		t.verticalCenter = 0;
		return t;
	};
	return HSliderSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/ItemRendererSkin.exml'] = window.skins.ItemRendererSkin = (function (_super) {
	__extends(ItemRendererSkin, _super);
	function ItemRendererSkin() {
		_super.call(this);
		this.skinParts = ["labelDisplay"];
		
		this.minHeight = 50;
		this.minWidth = 100;
		this.elementsContent = [this._Image1_i(),this.labelDisplay_i()];
		this.states = [
			new eui.State ("up",
				[
				])
			,
			new eui.State ("down",
				[
					new eui.SetProperty("_Image1","source","button_down_png")
				])
			,
			new eui.State ("disabled",
				[
					new eui.SetProperty("_Image1","alpha",0.5)
				])
		];
		
		eui.Binding.$bindProperties(this, ["hostComponent.data"],[0],this.labelDisplay,"text")
	}
	var _proto = ItemRendererSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		this._Image1 = t;
		t.percentHeight = 100;
		t.scale9Grid = new egret.Rectangle(1,3,8,8);
		t.source = "button_up_png";
		t.percentWidth = 100;
		return t;
	};
	_proto.labelDisplay_i = function () {
		var t = new eui.Label();
		this.labelDisplay = t;
		t.bottom = 8;
		t.fontFamily = "Tahoma";
		t.left = 8;
		t.right = 8;
		t.size = 20;
		t.textAlign = "center";
		t.textColor = 0xFFFFFF;
		t.top = 8;
		t.verticalAlign = "middle";
		return t;
	};
	return ItemRendererSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/PanelSkin.exml'] = window.skins.PanelSkin = (function (_super) {
	__extends(PanelSkin, _super);
	function PanelSkin() {
		_super.call(this);
		this.skinParts = ["titleDisplay","moveArea","closeButton"];
		
		this.minHeight = 230;
		this.minWidth = 450;
		this.elementsContent = [this._Image1_i(),this.moveArea_i(),this.closeButton_i()];
	}
	var _proto = PanelSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.scale9Grid = new egret.Rectangle(2,2,12,12);
		t.source = "border_png";
		t.top = 0;
		return t;
	};
	_proto.moveArea_i = function () {
		var t = new eui.Group();
		this.moveArea = t;
		t.height = 45;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.elementsContent = [this._Image2_i(),this.titleDisplay_i()];
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "header_png";
		t.top = 0;
		return t;
	};
	_proto.titleDisplay_i = function () {
		var t = new eui.Label();
		this.titleDisplay = t;
		t.fontFamily = "Tahoma";
		t.left = 15;
		t.right = 5;
		t.size = 20;
		t.textColor = 0xFFFFFF;
		t.verticalCenter = 0;
		t.wordWrap = false;
		return t;
	};
	_proto.closeButton_i = function () {
		var t = new eui.Button();
		this.closeButton = t;
		t.bottom = 5;
		t.horizontalCenter = 0;
		t.label = "close";
		return t;
	};
	return PanelSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/ProgressBarSkin.exml'] = window.skins.ProgressBarSkin = (function (_super) {
	__extends(ProgressBarSkin, _super);
	function ProgressBarSkin() {
		_super.call(this);
		this.skinParts = ["thumb","labelDisplay"];
		
		this.minHeight = 18;
		this.minWidth = 30;
		this.elementsContent = [this._Image1_i(),this.thumb_i(),this.labelDisplay_i()];
	}
	var _proto = ProgressBarSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.percentHeight = 100;
		t.scale9Grid = new egret.Rectangle(1,1,4,4);
		t.source = "track_pb_png";
		t.verticalCenter = 0;
		t.percentWidth = 100;
		return t;
	};
	_proto.thumb_i = function () {
		var t = new eui.Image();
		this.thumb = t;
		t.percentHeight = 100;
		t.source = "thumb_pb_png";
		t.percentWidth = 100;
		return t;
	};
	_proto.labelDisplay_i = function () {
		var t = new eui.Label();
		this.labelDisplay = t;
		t.fontFamily = "Tahoma";
		t.horizontalCenter = 0;
		t.size = 15;
		t.textAlign = "center";
		t.textColor = 0x707070;
		t.verticalAlign = "middle";
		t.verticalCenter = 0;
		return t;
	};
	return ProgressBarSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/RadioButtonSkin.exml'] = window.skins.RadioButtonSkin = (function (_super) {
	__extends(RadioButtonSkin, _super);
	function RadioButtonSkin() {
		_super.call(this);
		this.skinParts = ["labelDisplay"];
		
		this.elementsContent = [this._Group1_i()];
		this.states = [
			new eui.State ("up",
				[
				])
			,
			new eui.State ("down",
				[
					new eui.SetProperty("_Image1","alpha",0.7)
				])
			,
			new eui.State ("disabled",
				[
					new eui.SetProperty("_Image1","alpha",0.5)
				])
			,
			new eui.State ("upAndSelected",
				[
					new eui.SetProperty("_Image1","source","radiobutton_select_up_png")
				])
			,
			new eui.State ("downAndSelected",
				[
					new eui.SetProperty("_Image1","source","radiobutton_select_down_png")
				])
			,
			new eui.State ("disabledAndSelected",
				[
					new eui.SetProperty("_Image1","source","radiobutton_select_disabled_png")
				])
		];
	}
	var _proto = RadioButtonSkin.prototype;

	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.percentHeight = 100;
		t.percentWidth = 100;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this._Image1_i(),this.labelDisplay_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		this._Image1 = t;
		t.alpha = 1;
		t.fillMode = "scale";
		t.source = "radiobutton_unselect_png";
		return t;
	};
	_proto.labelDisplay_i = function () {
		var t = new eui.Label();
		this.labelDisplay = t;
		t.fontFamily = "Tahoma";
		t.size = 20;
		t.textAlign = "center";
		t.textColor = 0x707070;
		t.verticalAlign = "middle";
		return t;
	};
	return RadioButtonSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/ScrollerSkin.exml'] = window.skins.ScrollerSkin = (function (_super) {
	__extends(ScrollerSkin, _super);
	function ScrollerSkin() {
		_super.call(this);
		this.skinParts = ["horizontalScrollBar","verticalScrollBar"];
		
		this.minHeight = 20;
		this.minWidth = 20;
		this.elementsContent = [this.horizontalScrollBar_i(),this.verticalScrollBar_i()];
	}
	var _proto = ScrollerSkin.prototype;

	_proto.horizontalScrollBar_i = function () {
		var t = new eui.HScrollBar();
		this.horizontalScrollBar = t;
		t.bottom = 0;
		t.percentWidth = 100;
		return t;
	};
	_proto.verticalScrollBar_i = function () {
		var t = new eui.VScrollBar();
		this.verticalScrollBar = t;
		t.percentHeight = 100;
		t.right = 0;
		return t;
	};
	return ScrollerSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/TextInputSkin.exml'] = window.skins.TextInputSkin = (function (_super) {
	__extends(TextInputSkin, _super);
	function TextInputSkin() {
		_super.call(this);
		this.skinParts = ["textDisplay","promptDisplay"];
		
		this.minHeight = 40;
		this.minWidth = 300;
		this.elementsContent = [this._Image1_i(),this._Rect1_i(),this.textDisplay_i()];
		this.promptDisplay_i();
		
		this.states = [
			new eui.State ("normal",
				[
				])
			,
			new eui.State ("disabled",
				[
					new eui.SetProperty("textDisplay","textColor",0xff0000)
				])
			,
			new eui.State ("normalWithPrompt",
				[
					new eui.AddItems("promptDisplay","",1,"")
				])
			,
			new eui.State ("disabledWithPrompt",
				[
					new eui.AddItems("promptDisplay","",1,"")
				])
		];
	}
	var _proto = TextInputSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.percentHeight = 100;
		t.scale9Grid = new egret.Rectangle(1,3,8,8);
		t.source = "button_up_png";
		t.percentWidth = 100;
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0xffffff;
		t.percentHeight = 100;
		t.percentWidth = 100;
		return t;
	};
	_proto.textDisplay_i = function () {
		var t = new eui.EditableText();
		this.textDisplay = t;
		t.height = 24;
		t.left = "10";
		t.right = "10";
		t.size = 20;
		t.textColor = 0x000000;
		t.verticalCenter = "0";
		t.percentWidth = 100;
		return t;
	};
	_proto.promptDisplay_i = function () {
		var t = new eui.Label();
		this.promptDisplay = t;
		t.height = 24;
		t.left = 10;
		t.right = 10;
		t.size = 20;
		t.textColor = 0xa9a9a9;
		t.touchEnabled = false;
		t.verticalCenter = 0;
		t.percentWidth = 100;
		return t;
	};
	return TextInputSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/ToggleSwitchSkin.exml'] = window.skins.ToggleSwitchSkin = (function (_super) {
	__extends(ToggleSwitchSkin, _super);
	function ToggleSwitchSkin() {
		_super.call(this);
		this.skinParts = [];
		
		this.elementsContent = [this._Image1_i(),this._Image2_i()];
		this.states = [
			new eui.State ("up",
				[
					new eui.SetProperty("_Image1","source","off_png")
				])
			,
			new eui.State ("down",
				[
					new eui.SetProperty("_Image1","source","off_png")
				])
			,
			new eui.State ("disabled",
				[
					new eui.SetProperty("_Image1","source","off_png")
				])
			,
			new eui.State ("upAndSelected",
				[
					new eui.SetProperty("_Image2","horizontalCenter",18)
				])
			,
			new eui.State ("downAndSelected",
				[
					new eui.SetProperty("_Image2","horizontalCenter",18)
				])
			,
			new eui.State ("disabledAndSelected",
				[
					new eui.SetProperty("_Image2","horizontalCenter",18)
				])
		];
	}
	var _proto = ToggleSwitchSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		this._Image1 = t;
		t.source = "on_png";
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		this._Image2 = t;
		t.horizontalCenter = -18;
		t.source = "handle_png";
		t.verticalCenter = 0;
		return t;
	};
	return ToggleSwitchSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/VScrollBarSkin.exml'] = window.skins.VScrollBarSkin = (function (_super) {
	__extends(VScrollBarSkin, _super);
	function VScrollBarSkin() {
		_super.call(this);
		this.skinParts = ["thumb"];
		
		this.minHeight = 20;
		this.minWidth = 8;
		this.elementsContent = [this.thumb_i()];
	}
	var _proto = VScrollBarSkin.prototype;

	_proto.thumb_i = function () {
		var t = new eui.Image();
		this.thumb = t;
		t.height = 30;
		t.horizontalCenter = 0;
		t.scale9Grid = new egret.Rectangle(3,3,2,2);
		t.source = "roundthumb_png";
		t.visible = false;
		t.width = 8;
		return t;
	};
	return VScrollBarSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/VSliderSkin.exml'] = window.skins.VSliderSkin = (function (_super) {
	__extends(VSliderSkin, _super);
	function VSliderSkin() {
		_super.call(this);
		this.skinParts = ["track","thumb"];
		
		this.minHeight = 30;
		this.minWidth = 25;
		this.elementsContent = [this.track_i(),this.thumb_i()];
	}
	var _proto = VSliderSkin.prototype;

	_proto.track_i = function () {
		var t = new eui.Image();
		this.track = t;
		t.percentHeight = 100;
		t.horizontalCenter = 0;
		t.scale9Grid = new egret.Rectangle(1,1,4,4);
		t.source = "track_png";
		t.width = 7;
		return t;
	};
	_proto.thumb_i = function () {
		var t = new eui.Image();
		this.thumb = t;
		t.horizontalCenter = 0;
		t.source = "thumb_png";
		return t;
	};
	return VSliderSkin;
})(eui.Skin);generateEUI.paths['resource/GameSkins/AlertSkin.exml'] = window.AlertSkin = (function (_super) {
	__extends(AlertSkin, _super);
	function AlertSkin() {
		_super.call(this);
		this.skinParts = ["img_dialog_outer","lb_dialog_text","sure","alert_panel"];
		
		this.height = 1280;
		this.width = 720;
		this.elementsContent = [this.img_dialog_outer_i(),this.alert_panel_i()];
	}
	var _proto = AlertSkin.prototype;

	_proto.img_dialog_outer_i = function () {
		var t = new eui.Image();
		this.img_dialog_outer = t;
		t.bottom = 0;
		t.fillMode = "repeat";
		t.left = 0;
		t.right = 0;
		t.source = "dialog_bg_png";
		t.top = 0;
		return t;
	};
	_proto.alert_panel_i = function () {
		var t = new eui.Group();
		this.alert_panel = t;
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 446.97;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 666.66;
		t.elementsContent = [this._Image1_i(),this._Scroller1_i(),this.sure_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.anchorOffsetY = 0;
		t.bottom = -137.02999999999997;
		t.left = 0;
		t.right = -0.34000000000003183;
		t.scale9Grid = new egret.Rectangle(28,82,518,270);
		t.source = "dialog_panel_png";
		t.top = 0;
		return t;
	};
	_proto._Scroller1_i = function () {
		var t = new eui.Scroller();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 188;
		t.width = 564;
		t.x = 49.33;
		t.y = 99;
		t.viewport = this._Group1_i();
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.elementsContent = [this.lb_dialog_text_i()];
		return t;
	};
	_proto.lb_dialog_text_i = function () {
		var t = new eui.Label();
		this.lb_dialog_text = t;
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.bottom = 0;
		t.fontFamily = "Microsoft YaHei";
		t.left = 0;
		t.lineSpacing = 10;
		t.multiline = true;
		t.right = 0;
		t.text = "";
		t.top = 0;
		t.wordWrap = true;
		return t;
	};
	_proto.sure_i = function () {
		var t = new eui.Image();
		this.sure = t;
		t.bottom = 10;
		t.horizontalCenter = 0;
		t.source = "sure_png";
		return t;
	};
	return AlertSkin;
})(eui.Skin);generateEUI.paths['resource/GameSkins/ETimer.exml'] = window.ETimerSkin = (function (_super) {
	__extends(ETimerSkin, _super);
	function ETimerSkin() {
		_super.call(this);
		this.skinParts = ["lbTime"];
		
		this.height = 97;
		this.width = 99;
		this.elementsContent = [this._Image1_i(),this.lbTime_i()];
	}
	var _proto = ETimerSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.height = 97;
		t.horizontalCenter = 0;
		t.source = "timer_png";
		t.verticalCenter = 0;
		t.width = 99;
		return t;
	};
	_proto.lbTime_i = function () {
		var t = new eui.Label();
		this.lbTime = t;
		t.horizontalCenter = 4.5;
		t.text = "--";
		t.verticalCenter = 4.5;
		return t;
	};
	return ETimerSkin;
})(eui.Skin);generateEUI.paths['resource/GameSkins/TurnTableSkin.exml'] = window.TurnTableSkin = (function (_super) {
	__extends(TurnTableSkin, _super);
	var TurnTableSkin$Skin1 = 	(function (_super) {
		__extends(TurnTableSkin$Skin1, _super);
		function TurnTableSkin$Skin1() {
			_super.call(this);
			this.skinParts = ["labelDisplay"];
			
			this.elementsContent = [this._Image1_i(),this.labelDisplay_i()];
			this.states = [
				new eui.State ("up",
					[
					])
				,
				new eui.State ("down",
					[
						new eui.SetProperty("_Image1","source","lucky_button_down_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = TurnTableSkin$Skin1.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "lucky_button_up_png";
			t.percentWidth = 100;
			return t;
		};
		_proto.labelDisplay_i = function () {
			var t = new eui.Label();
			this.labelDisplay = t;
			t.horizontalCenter = 0;
			t.verticalCenter = 0;
			return t;
		};
		return TurnTableSkin$Skin1;
	})(eui.Skin);

	function TurnTableSkin() {
		_super.call(this);
		this.skinParts = ["gift_box","lucky_button"];
		
		this.height = 606;
		this.width = 606;
		this.elementsContent = [this.gift_box_i(),this.lucky_button_i()];
	}
	var _proto = TurnTableSkin.prototype;

	_proto.gift_box_i = function () {
		var t = new eui.Group();
		this.gift_box = t;
		t.anchorOffsetX = 303;
		t.anchorOffsetY = 303;
		t.height = 606;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 606;
		t.elementsContent = [this._Image1_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "turntable_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.lucky_button_i = function () {
		var t = new eui.Button();
		this.lucky_button = t;
		t.horizontalCenter = 0;
		t.label = "";
		t.verticalCenter = 0;
		t.skinName = TurnTableSkin$Skin1;
		return t;
	};
	return TurnTableSkin;
})(eui.Skin);generateEUI.paths['resource/GameSkins/HistorySkin.exml'] = window.HistorySkin = (function (_super) {
	__extends(HistorySkin, _super);
	function HistorySkin() {
		_super.call(this);
		this.skinParts = ["his_list","his_src","loading_txt","load_img"];
		
		this.height = 400;
		this.width = 628;
		this.elementsContent = [this._Image1_i(),this.his_src_i(),this.loading_txt_i(),this._Group2_i()];
	}
	var _proto = HistorySkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.scale9Grid = new egret.Rectangle(36,83,562,85);
		t.source = "history_png";
		t.top = 0;
		return t;
	};
	_proto.his_src_i = function () {
		var t = new eui.Scroller();
		this.his_src = t;
		t.bottom = 20;
		t.left = 20;
		t.right = 20;
		t.top = 80;
		t.viewport = this._Group1_i();
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.elementsContent = [this.his_list_i()];
		return t;
	};
	_proto.his_list_i = function () {
		var t = new eui.List();
		this.his_list = t;
		t.horizontalCenter = 0;
		t.top = 0;
		t.percentWidth = 100;
		t.x = 435;
		return t;
	};
	_proto.loading_txt_i = function () {
		var t = new eui.Label();
		this.loading_txt = t;
		t.horizontalCenter = 0;
		t.text = "正在加载中。。。";
		t.verticalCenter = 30;
		t.visible = false;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.height = 86;
		t.horizontalCenter = 0;
		t.verticalCenter = 30;
		t.visible = false;
		t.width = 86;
		t.elementsContent = [this._Rect1_i(),this.load_img_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 86;
		t.fillAlpha = 0.5;
		t.fillColor = 0x444444;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto.load_img_i = function () {
		var t = new eui.Image();
		this.load_img = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "loading_png";
		t.verticalCenter = 0;
		return t;
	};
	return HistorySkin;
})(eui.Skin);generateEUI.paths['resource/GameSkins/GameUiSkin.exml'] = window.GameUiSkin = (function (_super) {
	__extends(GameUiSkin, _super);
	var GameUiSkin$Skin2 = 	(function (_super) {
		__extends(GameUiSkin$Skin2, _super);
		function GameUiSkin$Skin2() {
			_super.call(this);
			this.skinParts = ["labelDisplay"];
			
			this.elementsContent = [this._Image1_i(),this.labelDisplay_i()];
			this.states = [
				new eui.State ("up",
					[
					])
				,
				new eui.State ("down",
					[
						new eui.SetProperty("_Image1","source","back_down_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = GameUiSkin$Skin2.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "back_up_png";
			t.percentWidth = 100;
			return t;
		};
		_proto.labelDisplay_i = function () {
			var t = new eui.Label();
			this.labelDisplay = t;
			t.horizontalCenter = 0;
			t.verticalCenter = 0;
			return t;
		};
		return GameUiSkin$Skin2;
	})(eui.Skin);

	var GameUiSkin$Skin3 = 	(function (_super) {
		__extends(GameUiSkin$Skin3, _super);
		function GameUiSkin$Skin3() {
			_super.call(this);
			this.skinParts = ["labelDisplay"];
			
			this.elementsContent = [this._Image1_i(),this.labelDisplay_i()];
			this.states = [
				new eui.State ("up",
					[
					])
				,
				new eui.State ("down",
					[
						new eui.SetProperty("_Image1","source","music_crr001_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = GameUiSkin$Skin3.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "music_crr000_png";
			t.percentWidth = 100;
			return t;
		};
		_proto.labelDisplay_i = function () {
			var t = new eui.Label();
			this.labelDisplay = t;
			t.horizontalCenter = 0;
			t.verticalCenter = 0;
			return t;
		};
		return GameUiSkin$Skin3;
	})(eui.Skin);

	function GameUiSkin() {
		_super.call(this);
		this.skinParts = ["bg_img","turntable","balance","times","help","back_btn","music_btn"];
		
		this.height = 1280;
		this.width = 720;
		this.elementsContent = [this.bg_img_i(),this._Scroller1_i()];
	}
	var _proto = GameUiSkin.prototype;

	_proto.bg_img_i = function () {
		var t = new eui.Image();
		this.bg_img = t;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "bg2_png";
		t.top = 0;
		return t;
	};
	_proto._Scroller1_i = function () {
		var t = new eui.Scroller();
		t.bottom = 0;
		t.height = 1280;
		t.left = 0;
		t.right = 0;
		t.scrollPolicyH = "off";
		t.top = 0;
		t.width = 720;
		t.viewport = this._Group1_i();
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.elementsContent = [this._Image1_i(),this._Image2_i(),this._Image3_i(),this.turntable_i(),this._HistoryPanel1_i(),this.balance_i(),this.times_i(),this.help_i(),this.back_btn_i(),this.music_btn_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "bg_png";
		t.top = 0;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.anchorOffsetX = 347.5;
		t.anchorOffsetY = 374;
		t.horizontalCenter = 0;
		t.source = "light1_png";
		t.top = 30;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.anchorOffsetX = 347.5;
		t.anchorOffsetY = 374;
		t.horizontalCenter = 0;
		t.source = "light2_png";
		t.top = 30;
		return t;
	};
	_proto.turntable_i = function () {
		var t = new TurnTable();
		this.turntable = t;
		t.horizontalCenter = 0;
		t.skinName = "TurnTableSkin";
		t.top = 75;
		return t;
	};
	_proto._HistoryPanel1_i = function () {
		var t = new HistoryPanel();
		t.horizontalCenter = 0;
		t.skinName = "HistorySkin";
		t.y = 1124;
		return t;
	};
	_proto.balance_i = function () {
		var t = new eui.Label();
		this.balance = t;
		t.size = 25;
		t.text = "--";
		t.textAlign = "center";
		t.textColor = 0xd8b043;
		t.verticalAlign = "middle";
		t.width = 140;
		t.x = 366;
		t.y = 924;
		return t;
	};
	_proto.times_i = function () {
		var t = new eui.Label();
		this.times = t;
		t.height = 25;
		t.size = 25;
		t.text = "--";
		t.textAlign = "center";
		t.textColor = 0xcc1bd6;
		t.verticalAlign = "middle";
		t.width = 30;
		t.x = 398.52;
		t.y = 975;
		return t;
	};
	_proto.help_i = function () {
		var t = new eui.Group();
		this.help = t;
		t.height = 200;
		t.horizontalCenter = 0;
		t.width = 628;
		t.y = 1576;
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this._Label1_i(),this._Label2_i(),this._Label3_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.gap = 20;
		t.horizontalAlign = "left";
		t.paddingLeft = 20;
		t.verticalAlign = "top";
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.text = "玩法说明：";
		t.textColor = 0xcebebe;
		t.x = 112;
		t.y = 47;
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.text = "1.每人每天有3次抽奖机会，点击“抽奖”按钮开始抽奖。";
		t.textColor = 0xcebebe;
		t.percentWidth = 100;
		t.x = 122;
		t.y = 57;
		return t;
	};
	_proto._Label3_i = function () {
		var t = new eui.Label();
		t.text = "2.指针停止位置，获得相应奖品。";
		t.textColor = 0xcebebe;
		t.percentWidth = 100;
		t.x = 122;
		t.y = 57;
		return t;
	};
	_proto.back_btn_i = function () {
		var t = new eui.Button();
		this.back_btn = t;
		t.label = "";
		t.x = 30;
		t.y = 42;
		t.skinName = GameUiSkin$Skin2;
		return t;
	};
	_proto.music_btn_i = function () {
		var t = new eui.ToggleSwitch();
		this.music_btn = t;
		t.label = "";
		t.x = 620;
		t.y = 42;
		t.skinName = GameUiSkin$Skin3;
		return t;
	};
	return GameUiSkin;
})(eui.Skin);generateEUI.paths['resource/GameSkins/GiftSkin.exml'] = window.GiftSkin = (function (_super) {
	__extends(GiftSkin, _super);
	function GiftSkin() {
		_super.call(this);
		this.skinParts = ["gift_img"];
		
		this.height = 606;
		this.width = 606;
		this.elementsContent = [this._Group1_i()];
	}
	var _proto = GiftSkin.prototype;

	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 303;
		t.anchorOffsetY = 303;
		t.height = 606;
		t.width = 606;
		t.x = 303;
		t.y = 303;
		t.elementsContent = [this.gift_img_i()];
		return t;
	};
	_proto.gift_img_i = function () {
		var t = new eui.Image();
		this.gift_img = t;
		t.anchorOffsetX = 58;
		t.anchorOffsetY = 60;
		t.horizontalCenter = 10;
		t.rotation = -90;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "poker_png";
		t.top = 30;
		return t;
	};
	return GiftSkin;
})(eui.Skin);generateEUI.paths['resource/GameSkins/HisItemSkin.exml'] = window.HisItemSkin = (function (_super) {
	__extends(HisItemSkin, _super);
	function HisItemSkin() {
		_super.call(this);
		this.skinParts = ["user_name","award_type","award_time"];
		
		this.height = 50;
		this.width = 588;
		this.elementsContent = [this._Group1_i()];
	}
	var _proto = HisItemSkin.prototype;

	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 10;
		t.right = 10;
		t.top = 0;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this.user_name_i(),this.award_type_i(),this.award_time_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "center";
		t.paddingLeft = 0;
		t.verticalAlign = "middle";
		return t;
	};
	_proto.user_name_i = function () {
		var t = new eui.Label();
		this.user_name = t;
		t.height = 22;
		t.size = 22;
		t.text = "用户名用户名";
		t.textAlign = "left";
		t.textColor = 0x7fbbe0;
		t.verticalAlign = "middle";
		t.width = 120;
		t.x = 43;
		t.y = 14;
		return t;
	};
	_proto.award_type_i = function () {
		var t = new eui.Label();
		this.award_type = t;
		t.height = 22;
		t.size = 22;
		t.text = "获得欢乐攻城免费卡1张";
		t.textAlign = "center";
		t.textColor = 0x7fbbe0;
		t.verticalAlign = "middle";
		t.width = 300;
		t.x = 53;
		t.y = 24;
		return t;
	};
	_proto.award_time_i = function () {
		var t = new eui.Label();
		this.award_time = t;
		t.height = 22;
		t.size = 22;
		t.text = "12分钟前";
		t.textAlign = "right";
		t.textColor = 0x7fbbe0;
		t.verticalAlign = "middle";
		t.width = 125;
		t.x = 63;
		t.y = 34;
		return t;
	};
	return HisItemSkin;
})(eui.Skin);generateEUI.paths['resource/GameSkins/LoadingSkin.exml'] = window.LoadingSkin = (function (_super) {
	__extends(LoadingSkin, _super);
	function LoadingSkin() {
		_super.call(this);
		this.skinParts = ["bar","progress"];
		
		this.height = 1280;
		this.width = 720;
		this.elementsContent = [this._Image1_i(),this._Group2_i(),this._Image3_i(),this._Image4_i()];
	}
	var _proto = LoadingSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "load_ui_bg_png";
		t.top = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.anchorOffsetY = 0;
		t.bottom = 108;
		t.height = 184;
		t.left = 0;
		t.right = 0;
		t.layout = this._BasicLayout1_i();
		t.elementsContent = [this._Image2_i(),this._Group1_i(),this._Label1_i(),this.progress_i()];
		return t;
	};
	_proto._BasicLayout1_i = function () {
		var t = new eui.BasicLayout();
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 67.26;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "load_bg_png";
		t.verticalCenter = 7.5;
		t.width = 509;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.height = 59;
		t.horizontalCenter = -0.5;
		t.scaleX = 1;
		t.scaleY = 1;
		t.verticalCenter = 2.5;
		t.width = 502;
		t.elementsContent = [this.bar_i()];
		return t;
	};
	_proto.bar_i = function () {
		var t = new eui.Image();
		this.bar = t;
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 48.81;
		t.left = 0;
		t.scale9Grid = new egret.Rectangle(8,1,584,58);
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "loading_bar_png";
		t.verticalCenter = 0;
		t.width = 502;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.bottom = 20;
		t.fontFamily = "KaiTi";
		t.height = 25;
		t.left = 120;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 25;
		t.text = "正在加载资源。。。";
		t.textAlign = "center";
		t.textColor = 0xffdddd;
		t.verticalAlign = "bottom";
		t.width = 225;
		return t;
	};
	_proto.progress_i = function () {
		var t = new eui.Label();
		this.progress = t;
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.bottom = 20;
		t.fontFamily = "KaiTi";
		t.height = 25;
		t.right = 110;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 25;
		t.text = "0%";
		t.textAlign = "center";
		t.textColor = 0xFFDDDD;
		t.verticalAlign = "middle";
		t.width = 50;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 106.18;
		t.horizontalCenter = 0.5;
		t.source = "loading_msg_png";
		t.top = 201;
		t.width = 557;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.alpha = 0.5;
		t.horizontalCenter = 0;
		t.source = "load_ui_box_png";
		t.verticalCenter = 0;
		t.visible = false;
		return t;
	};
	return LoadingSkin;
})(eui.Skin);generateEUI.paths['resource/GameSkins/ShowSkin.exml'] = window.ShowSkin = (function (_super) {
	__extends(ShowSkin, _super);
	var ShowSkin$Skin4 = 	(function (_super) {
		__extends(ShowSkin$Skin4, _super);
		function ShowSkin$Skin4() {
			_super.call(this);
			this.skinParts = ["labelDisplay"];
			
			this.elementsContent = [this._Image1_i(),this.labelDisplay_i()];
			this.states = [
				new eui.State ("up",
					[
					])
				,
				new eui.State ("down",
					[
						new eui.SetProperty("_Image1","source","leave_down_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = ShowSkin$Skin4.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "leave_up_png";
			t.percentWidth = 100;
			return t;
		};
		_proto.labelDisplay_i = function () {
			var t = new eui.Label();
			this.labelDisplay = t;
			t.horizontalCenter = 0;
			t.verticalCenter = 0;
			return t;
		};
		return ShowSkin$Skin4;
	})(eui.Skin);

	var ShowSkin$Skin5 = 	(function (_super) {
		__extends(ShowSkin$Skin5, _super);
		function ShowSkin$Skin5() {
			_super.call(this);
			this.skinParts = ["labelDisplay"];
			
			this.elementsContent = [this._Image1_i(),this.labelDisplay_i()];
			this.states = [
				new eui.State ("up",
					[
					])
				,
				new eui.State ("down",
					[
						new eui.SetProperty("_Image1","source","use_down2_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = ShowSkin$Skin5.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "use_up2_png";
			t.percentWidth = 100;
			return t;
		};
		_proto.labelDisplay_i = function () {
			var t = new eui.Label();
			this.labelDisplay = t;
			t.horizontalCenter = 0;
			t.verticalCenter = 0;
			return t;
		};
		return ShowSkin$Skin5;
	})(eui.Skin);

	function ShowSkin() {
		_super.call(this);
		this.skinParts = ["img","txt","dele_btn","use_btn","main_group"];
		
		this.height = 1280;
		this.width = 720;
		this.elementsContent = [this._Image1_i(),this.main_group_i()];
	}
	var _proto = ShowSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.alpha = 0.5;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "bg2_png";
		t.top = 0;
		return t;
	};
	_proto.main_group_i = function () {
		var t = new eui.Group();
		this.main_group = t;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.elementsContent = [this._Image2_i(),this._Group1_i(),this.dele_btn_i(),this.use_btn_i()];
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "bingo_png";
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.height = 130;
		t.horizontalCenter = 0;
		t.width = 420;
		t.y = 597;
		t.elementsContent = [this.img_i(),this.txt_i()];
		return t;
	};
	_proto.img_i = function () {
		var t = new eui.Image();
		this.img = t;
		t.height = 120;
		t.left = 10;
		t.source = "gongcheng_png";
		t.verticalCenter = 10;
		t.width = 116;
		return t;
	};
	_proto.txt_i = function () {
		var t = new eui.Label();
		this.txt = t;
		t.right = 10;
		t.size = 30;
		t.text = "奔驰宝马免费卡1张";
		t.textAlign = "center";
		t.textColor = 0x7300c8;
		t.verticalAlign = "middle";
		t.verticalCenter = 0;
		return t;
	};
	_proto.dele_btn_i = function () {
		var t = new eui.Button();
		this.dele_btn = t;
		t.label = "";
		t.x = 592;
		t.y = 39;
		t.skinName = ShowSkin$Skin4;
		return t;
	};
	_proto.use_btn_i = function () {
		var t = new eui.Button();
		this.use_btn = t;
		t.horizontalCenter = 0;
		t.label = "";
		t.visible = false;
		t.y = 757.84;
		t.skinName = ShowSkin$Skin5;
		return t;
	};
	return ShowSkin;
})(eui.Skin);