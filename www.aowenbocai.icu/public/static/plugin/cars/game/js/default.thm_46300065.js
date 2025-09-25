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
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/AlertSkin.exml'] = window.AlertSkin = (function (_super) {
	__extends(AlertSkin, _super);
	var AlertSkin$Skin1 = 	(function (_super) {
		__extends(AlertSkin$Skin1, _super);
		function AlertSkin$Skin1() {
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
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = AlertSkin$Skin1.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			t.percentHeight = 100;
			t.source = "sure_png";
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
		return AlertSkin$Skin1;
	})(eui.Skin);

	var AlertSkin$Skin2 = 	(function (_super) {
		__extends(AlertSkin$Skin2, _super);
		function AlertSkin$Skin2() {
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
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = AlertSkin$Skin2.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			t.percentHeight = 100;
			t.source = "dialog_cancel_png";
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
		return AlertSkin$Skin2;
	})(eui.Skin);

	function AlertSkin() {
		_super.call(this);
		this.skinParts = ["img_dialog_outer","lb_dialog_text","btn_dialog_cancel"];
		
		this.height = 750;
		this.width = 1334;
		this.elementsContent = [this.img_dialog_outer_i(),this._Group2_i()];
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
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 446.97;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 666.66;
		t.elementsContent = [this._Image1_i(),this._Scroller1_i(),this.btn_dialog_cancel_i(),this._EButton1_i()];
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
	_proto.btn_dialog_cancel_i = function () {
		var t = new EButton();
		this.btn_dialog_cancel = t;
		t.horizontalCenter = 0;
		t.y = 340;
		t.skinName = AlertSkin$Skin1;
		return t;
	};
	_proto._EButton1_i = function () {
		var t = new EButton();
		t.anchorOffsetY = 0;
		t.height = 55;
		t.visible = false;
		t.width = 55;
		t.x = 621.32;
		t.y = -10.66;
		t.skinName = AlertSkin$Skin2;
		return t;
	};
	return AlertSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/BetsSkin.exml'] = window.BetsSkin = (function (_super) {
	__extends(BetsSkin, _super);
	function BetsSkin() {
		_super.call(this);
		this.skinParts = ["allBet","youBet","logo","logoBet"];
		
		this.currentState = "bet";
		this.height = 140;
		this.width = 80;
		this.elementsContent = [];
		this._Group2_i();
		
		this.states = [
			new eui.State ("bet",
				[
					new eui.AddItems("_Group2","",0,""),
					new eui.SetProperty("","width",80),
					new eui.SetProperty("","height",150)
				])
		];
	}
	var _proto = BetsSkin.prototype;

	_proto._Group2_i = function () {
		var t = new eui.Group();
		this._Group2 = t;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this.allBet_i(),this.youBet_i(),this._Group1_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.gap = 0;
		t.horizontalAlign = "center";
		t.paddingTop = 0;
		t.verticalAlign = "bottom";
		return t;
	};
	_proto.allBet_i = function () {
		var t = new eui.Label();
		this.allBet = t;
		t.height = 30;
		t.size = 20;
		t.text = "0";
		t.textAlign = "center";
		t.verticalAlign = "middle";
		t.width = 80;
		t.y = 2;
		return t;
	};
	_proto.youBet_i = function () {
		var t = new eui.Label();
		this.youBet = t;
		t.height = 30;
		t.size = 20;
		t.text = "0";
		t.textAlign = "center";
		t.textColor = 0x05ff63;
		t.verticalAlign = "middle";
		t.width = 80;
		t.y = 2;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.height = 80;
		t.width = 80;
		t.x = 1;
		t.y = 78;
		t.elementsContent = [this._Image1_i(),this.logo_i(),this.logoBet_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "bet_json.bet34";
		t.verticalCenter = 0;
		return t;
	};
	_proto.logo_i = function () {
		var t = new eui.Image();
		this.logo = t;
		t.horizontalCenter = 0;
		t.source = "bet_json.bet8";
		t.verticalCenter = 0;
		return t;
	};
	_proto.logoBet_i = function () {
		var t = new eui.Label();
		this.logoBet = t;
		t.bold = false;
		t.border = false;
		t.bottom = 5;
		t.horizontalCenter = 0;
		t.size = 22;
		t.stroke = 3;
		t.text = "22";
		t.textColor = 0xffffff;
		return t;
	};
	return BetsSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/BetGroupSkin.exml'] = window.BetGroupSkin = (function (_super) {
	__extends(BetGroupSkin, _super);
	var BetGroupSkin$Skin3 = 	(function (_super) {
		__extends(BetGroupSkin$Skin3, _super);
		function BetGroupSkin$Skin3() {
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
						new eui.SetProperty("_Image1","source","bet_json.bet1")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = BetGroupSkin$Skin3.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "bet_json.bet0";
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
		return BetGroupSkin$Skin3;
	})(eui.Skin);

	var BetGroupSkin$Skin4 = 	(function (_super) {
		__extends(BetGroupSkin$Skin4, _super);
		function BetGroupSkin$Skin4() {
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
						new eui.SetProperty("_Image1","source","bet_json.bet2")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = BetGroupSkin$Skin4.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "bet_json.bet3";
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
		return BetGroupSkin$Skin4;
	})(eui.Skin);

	function BetGroupSkin() {
		_super.call(this);
		this.skinParts = ["betGroup","bets","go_on","pointer","pointerText"];
		
		this.height = 146;
		this.width = 1334;
		this.elementsContent = [this._Image1_i(),this.betGroup_i(),this._Group1_i()];
	}
	var _proto = BetGroupSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "bet_bg_png";
		t.top = 0;
		return t;
	};
	_proto.betGroup_i = function () {
		var t = new eui.Group();
		this.betGroup = t;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.elementsContent = [this._Bets1_i(),this._Bets2_i(),this._Bets3_i(),this._Bets4_i(),this._Bets5_i(),this._Bets6_i(),this._Bets7_i(),this._Bets8_i()];
		return t;
	};
	_proto._Bets1_i = function () {
		var t = new Bets();
		t.left = 30;
		t.skinName = "BetsSkin";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Bets2_i = function () {
		var t = new Bets();
		t.left = 130;
		t.skinName = "BetsSkin";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Bets3_i = function () {
		var t = new Bets();
		t.left = 230;
		t.skinName = "BetsSkin";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Bets4_i = function () {
		var t = new Bets();
		t.left = 330;
		t.skinName = "BetsSkin";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Bets5_i = function () {
		var t = new Bets();
		t.right = 330;
		t.skinName = "BetsSkin";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Bets6_i = function () {
		var t = new Bets();
		t.right = 230;
		t.skinName = "BetsSkin";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Bets7_i = function () {
		var t = new Bets();
		t.right = 130;
		t.skinName = "BetsSkin";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Bets8_i = function () {
		var t = new Bets();
		t.right = 30;
		t.skinName = "BetsSkin";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 472;
		t.elementsContent = [this._Image2_i(),this.bets_i(),this.go_on_i(),this.pointer_i(),this.pointerText_i()];
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "bet_json.bet22";
		t.top = 0;
		return t;
	};
	_proto.bets_i = function () {
		var t = new eui.Button();
		this.bets = t;
		t.label = "";
		t.left = 10;
		t.verticalCenter = 10;
		t.skinName = BetGroupSkin$Skin3;
		return t;
	};
	_proto.go_on_i = function () {
		var t = new eui.Button();
		this.go_on = t;
		t.label = "";
		t.right = 10;
		t.verticalCenter = 10;
		t.skinName = BetGroupSkin$Skin4;
		return t;
	};
	_proto.pointer_i = function () {
		var t = new eui.Image();
		this.pointer = t;
		t.anchorOffsetX = 62.68;
		t.anchorOffsetY = 0.78;
		t.rotation = -30;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "bet_json.bet30";
		t.x = 239.69;
		t.y = 87.04;
		return t;
	};
	_proto.pointerText_i = function () {
		var t = new eui.Label();
		this.pointerText = t;
		t.horizontalCenter = 0;
		t.size = 20;
		t.text = "0";
		t.top = 40;
		return t;
	};
	return BetGroupSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/BetListSkin.exml'] = window.BetListSkin = (function (_super) {
	__extends(BetListSkin, _super);
	function BetListSkin() {
		_super.call(this);
		this.skinParts = ["home","codeList"];
		
		this.height = 750;
		this.width = 1334;
		this.elementsContent = [this.home_i(),this.codeList_i()];
	}
	var _proto = BetListSkin.prototype;

	_proto.home_i = function () {
		var t = new eui.Rect();
		this.home = t;
		t.bottom = 0;
		t.fillAlpha = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto.codeList_i = function () {
		var t = new eui.List();
		this.codeList = t;
		t.bottom = 150;
		t.horizontalCenter = -150;
		t.itemRendererSkinName = ItemSkin;
		return t;
	};
	return BetListSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/BonusSkin.exml'] = window.BonusSkin = (function (_super) {
	__extends(BonusSkin, _super);
	function BonusSkin() {
		_super.call(this);
		this.skinParts = ["remove","bonus","sure1","sure"];
		
		this.currentState = "index";
		this.height = 750;
		this.width = 1334;
		this.remove_i();
		this.elementsContent = [this._Rect1_i()];
		this._Image1_i();
		
		this._Image2_i();
		
		this._Group1_i();
		
		this.bonus_i();
		
		this._Label1_i();
		
		this._Group2_i();
		
		this.sure1_i();
		
		this._Image7_i();
		
		this._Image8_i();
		
		this._Image10_i();
		
		this._HorizontalLayout1_i();
		
		this._HorizontalLayout2_i();
		
		this._Group3_i();
		
		this.sure_i();
		
		this._Group4_i();
		
		this._Image15_i();
		
		this._Group5_i();
		
		this.states = [
			new eui.State ("index",
				[
					new eui.AddItems("_Image8","_Group3",2,"_Image9"),
					new eui.AddItems("_Group3","",1,""),
					new eui.AddItems("sure","",1,""),
					new eui.SetProperty("_Group3","layout",this._HorizontalLayout2),
					new eui.SetProperty("sure","bottom",50)
				])
			,
			new eui.State ("win",
				[
					new eui.AddItems("_Image1","_Group1",0,""),
					new eui.AddItems("_Image2","_Group1",2,"_Image3"),
					new eui.AddItems("_Group1","",1,""),
					new eui.AddItems("bonus","_Group2",1,""),
					new eui.AddItems("_Label1","_Group2",1,""),
					new eui.AddItems("_Group2","",1,""),
					new eui.AddItems("sure1","",1,""),
					new eui.AddItems("_Image8","_Group3",2,"_Image9"),
					new eui.SetProperty("_Group3","layout",this._HorizontalLayout1),
					new eui.SetProperty("sure","verticalCenter",0)
				])
			,
			new eui.State ("lose",
				[
					new eui.AddItems("_Image7","_Group3",2,"_Image9"),
					new eui.AddItems("_Image10","_Group3",1,""),
					new eui.AddItems("sure","",1,""),
					new eui.AddItems("_Group4","",1,""),
					new eui.AddItems("_Image15","_Group5",0,""),
					new eui.AddItems("_Group5","",1,""),
					new eui.SetProperty("_Image6","horizontalCenter",0),
					new eui.SetProperty("_Image6","verticalCenter",0),
					new eui.SetProperty("_Group3","horizontalCenter",0),
					new eui.SetProperty("_Group3","top",0),
					new eui.SetProperty("_Group3","width",200),
					new eui.SetProperty("_Group3","height",200),
					new eui.SetProperty("sure","horizontalCenter",0),
					new eui.SetProperty("sure","bottom",50)
				])
		];
	}
	var _proto = BonusSkin.prototype;

	_proto.remove_i = function () {
		var t = new egret.tween.TweenGroup();
		this.remove = t;
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		this._Group1 = t;
		t.horizontalCenter = 0;
		t.top = 200;
		t.elementsContent = [this._Image3_i(),this._Image4_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		this._Image1 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "win_json.win42";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		this._Image2 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "win_json.win39";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		this._Image3 = t;
		t.left = 82;
		t.source = "win_json.win9";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.right = 82;
		t.source = "win_json.win5";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		this._Group2 = t;
		t.height = 200;
		t.horizontalCenter = 0;
		t.verticalCenter = 100;
		t.width = 200;
		t.elementsContent = [this._Image5_i()];
		return t;
	};
	_proto._Image5_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "win_json.win45";
		t.verticalCenter = 0;
		return t;
	};
	_proto.bonus_i = function () {
		var t = new eui.Label();
		this.bonus = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 50;
		t.text = "20000";
		t.textAlign = "center";
		t.textColor = 0xef2113;
		t.verticalAlign = "middle";
		t.verticalCenter = -28;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		this._Label1 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 50;
		t.text = "金豆";
		t.textAlign = "center";
		t.textColor = 0xE09228;
		t.verticalAlign = "middle";
		t.verticalCenter = 31;
		return t;
	};
	_proto.sure1_i = function () {
		var t = new eui.Image();
		this.sure1 = t;
		t.bottom = 50;
		t.horizontalCenter = 0;
		t.source = "result_json.result1";
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		this._Group3 = t;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.elementsContent = [this._Image6_i(),this._Image9_i()];
		return t;
	};
	_proto._Image6_i = function () {
		var t = new eui.Image();
		this._Image6 = t;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "result_json.result4";
		t.x = -54.5;
		t.y = 75.5;
		return t;
	};
	_proto._Image7_i = function () {
		var t = new eui.Image();
		this._Image7 = t;
		t.horizontalCenter = 0;
		t.source = "result_json.result11";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image8_i = function () {
		var t = new eui.Image();
		this._Image8 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "result_json.result5";
		t.verticalCenter = -200;
		t.x = -30;
		t.y = 64;
		return t;
	};
	_proto._Image9_i = function () {
		var t = new eui.Image();
		this._Image9 = t;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "result_json.result3";
		t.x = 229;
		t.y = 79;
		return t;
	};
	_proto._Image10_i = function () {
		var t = new eui.Image();
		this._Image10 = t;
		t.source = "result_json.result17";
		t.x = 207;
		t.y = 20;
		return t;
	};
	_proto.sure_i = function () {
		var t = new eui.Image();
		this.sure = t;
		t.horizontalCenter = 0;
		t.source = "result_json.result1";
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		this._Group4 = t;
		t.horizontalCenter = 0;
		t.top = 200;
		t.elementsContent = [this._Image11_i(),this._Image12_i(),this._Image13_i(),this._Image14_i()];
		return t;
	};
	_proto._Image11_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "result_json.result11";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image12_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "result_json.result17";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image13_i = function () {
		var t = new eui.Image();
		t.right = 82;
		t.source = "result_json.result8";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image14_i = function () {
		var t = new eui.Image();
		t.left = 82;
		t.source = "result_json.result7";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		this._Group5 = t;
		t.horizontalCenter = 0;
		t.verticalCenter = 100;
		t.elementsContent = [this._Label2_i()];
		return t;
	};
	_proto._Image15_i = function () {
		var t = new eui.Image();
		this._Image15 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "result_json.result9";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = 0;
		t.size = 50;
		t.text = "未中奖";
		t.verticalCenter = 0;
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		this._HorizontalLayout1 = t;
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._HorizontalLayout2_i = function () {
		var t = new eui.HorizontalLayout();
		this._HorizontalLayout2 = t;
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	return BonusSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/CarImgSkin.exml'] = window.CarImgSkin = (function (_super) {
	__extends(CarImgSkin, _super);
	function CarImgSkin() {
		_super.call(this);
		this.skinParts = ["car"];
		
		this.height = 78;
		this.width = 78;
		this.elementsContent = [this.car_i()];
	}
	var _proto = CarImgSkin.prototype;

	_proto.car_i = function () {
		var t = new eui.Image();
		this.car = t;
		t.horizontalCenter = 0;
		t.source = "icon_cars_json.icon_cars0";
		t.verticalCenter = 0;
		return t;
	};
	return CarImgSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/CarLogoSkin.exml'] = window.CarLogoSkin = (function (_super) {
	__extends(CarLogoSkin, _super);
	function CarLogoSkin() {
		_super.call(this);
		this.skinParts = ["run","open","logo","image","image0","image2","effects","openImg","openImg1"];
		
		this.currentState = "index";
		this.height = 120;
		this.width = 120;
		this.run_i();
		this.open_i();
		this.elementsContent = [this._Group1_i()];
		this.openImg_i();
		
		this.openImg1_i();
		
		this.states = [
			new eui.State ("index",
				[
					new eui.SetProperty("effects","visible",false)
				])
			,
			new eui.State ("run",
				[
					new eui.SetProperty("_Group1","width",120),
					new eui.SetProperty("_Group1","height",120),
					new eui.SetProperty("","width",120),
					new eui.SetProperty("","height",120)
				])
			,
			new eui.State ("open",
				[
					new eui.AddItems("openImg","_Group1",1,""),
					new eui.AddItems("openImg1","_Group1",1,""),
					new eui.SetProperty("effects","visible",false)
				])
		];
		
		eui.Binding.$bindProperties(this, ["image"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object1,"alpha")
		eui.Binding.$bindProperties(this, ["image0"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object2,"alpha")
		eui.Binding.$bindProperties(this, [1.6],[],this._Object2,"scaleX")
		eui.Binding.$bindProperties(this, [1.6],[],this._Object2,"scaleY")
		eui.Binding.$bindProperties(this, ["image2"],[0],this._TweenItem3,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object3,"alpha")
		eui.Binding.$bindProperties(this, ["openImg"],[0],this._TweenItem4,"target")
		eui.Binding.$bindProperties(this, [0.5],[],this._Object4,"scaleX")
		eui.Binding.$bindProperties(this, [0.5],[],this._Object4,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object5,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object5,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object6,"alpha")
		eui.Binding.$bindProperties(this, [0.5],[],this._Object6,"scaleX")
		eui.Binding.$bindProperties(this, [0.5],[],this._Object6,"scaleY")
		eui.Binding.$bindProperties(this, ["openImg1"],[0],this._TweenItem5,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object7,"alpha")
		eui.Binding.$bindProperties(this, [3.5],[],this._Object7,"scaleX")
		eui.Binding.$bindProperties(this, [3.5],[],this._Object7,"scaleY")
	}
	var _proto = CarLogoSkin.prototype;

	_proto.run_i = function () {
		var t = new egret.tween.TweenGroup();
		this.run = t;
		t.items = [this._TweenItem1_i(),this._TweenItem2_i(),this._TweenItem3_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Set1_i(),this._To1_i()];
		return t;
	};
	_proto._Set1_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._To1_i = function () {
		var t = new egret.tween.To();
		t.duration = 750;
		t.props = this._Object1_i();
		return t;
	};
	_proto._Object1_i = function () {
		var t = {};
		this._Object1 = t;
		return t;
	};
	_proto._TweenItem2_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem2 = t;
		t.paths = [this._Set2_i(),this._To2_i()];
		return t;
	};
	_proto._Set2_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._To2_i = function () {
		var t = new egret.tween.To();
		t.duration = 750;
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto._TweenItem3_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem3 = t;
		t.paths = [this._Wait1_i(),this._Set3_i(),this._To3_i()];
		return t;
	};
	_proto._Wait1_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 300;
		return t;
	};
	_proto._Set3_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._To3_i = function () {
		var t = new egret.tween.To();
		t.duration = 600;
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto.open_i = function () {
		var t = new egret.tween.TweenGroup();
		this.open = t;
		t.items = [this._TweenItem4_i(),this._TweenItem5_i()];
		return t;
	};
	_proto._TweenItem4_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem4 = t;
		t.paths = [this._Set4_i(),this._To4_i(),this._To5_i()];
		return t;
	};
	_proto._Set4_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object4_i();
		return t;
	};
	_proto._Object4_i = function () {
		var t = {};
		this._Object4 = t;
		return t;
	};
	_proto._To4_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object5_i();
		return t;
	};
	_proto._Object5_i = function () {
		var t = {};
		this._Object5 = t;
		return t;
	};
	_proto._To5_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object6_i();
		return t;
	};
	_proto._Object6_i = function () {
		var t = {};
		this._Object6 = t;
		return t;
	};
	_proto._TweenItem5_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem5 = t;
		t.paths = [this._Set5_i(),this._To6_i()];
		return t;
	};
	_proto._Set5_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._To6_i = function () {
		var t = new egret.tween.To();
		t.duration = 500;
		t.props = this._Object7_i();
		return t;
	};
	_proto._Object7_i = function () {
		var t = {};
		this._Object7 = t;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		this._Group1 = t;
		t.anchorOffsetX = 60;
		t.anchorOffsetY = 60;
		t.height = 120;
		t.left = 0;
		t.top = 0;
		t.width = 120;
		t.elementsContent = [this.logo_i(),this.effects_i()];
		return t;
	};
	_proto.logo_i = function () {
		var t = new eui.Image();
		this.logo = t;
		t.horizontalCenter = 0;
		t.source = "icon_cars_json.icon_cars12";
		t.verticalCenter = 0;
		return t;
	};
	_proto.effects_i = function () {
		var t = new eui.Group();
		this.effects = t;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.elementsContent = [this.image_i(),this.image0_i(),this.image2_i()];
		return t;
	};
	_proto.image_i = function () {
		var t = new eui.Image();
		this.image = t;
		t.horizontalCenter = 0;
		t.source = "play_json.play9";
		t.verticalCenter = 0;
		return t;
	};
	_proto.image0_i = function () {
		var t = new eui.Image();
		this.image0 = t;
		t.horizontalCenter = 0;
		t.source = "play_json.play19";
		t.verticalCenter = 0;
		return t;
	};
	_proto.image2_i = function () {
		var t = new eui.Image();
		this.image2 = t;
		t.horizontalCenter = 0;
		t.source = "play_json.play17";
		t.verticalCenter = 0;
		return t;
	};
	_proto.openImg_i = function () {
		var t = new eui.Image();
		this.openImg = t;
		t.horizontalCenter = 0;
		t.scaleX = 0.5;
		t.scaleY = 0.5;
		t.source = "draw_json.draw0";
		t.verticalCenter = 0;
		return t;
	};
	_proto.openImg1_i = function () {
		var t = new eui.Image();
		this.openImg1 = t;
		t.horizontalCenter = 0;
		t.source = "play_json.play63";
		t.verticalCenter = 0;
		return t;
	};
	return CarLogoSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/LastOpenSkin.exml'] = window.LastOpenSkin = (function (_super) {
	__extends(LastOpenSkin, _super);
	function LastOpenSkin() {
		_super.call(this);
		this.skinParts = ["car_box","car_scoller","newImg","txt"];
		
		this.elementsContent = [this._Image1_i(),this._Group1_i()];
	}
	var _proto = LastOpenSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "other_json.other5";
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.horizontalCenter = 0;
		t.top = 41;
		t.elementsContent = [this._Image2_i(),this.car_scoller_i(),this.newImg_i(),this.txt_i()];
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.scale9Grid = new egret.Rectangle(10,7,63,24);
		t.source = "other_json.other3";
		t.top = 0;
		return t;
	};
	_proto.car_scoller_i = function () {
		var t = new eui.Scroller();
		this.car_scoller = t;
		t.height = 405;
		t.horizontalCenter = 0;
		t.top = 0;
		t.width = 83;
		t.viewport = this.car_box_i();
		return t;
	};
	_proto.car_box_i = function () {
		var t = new eui.Group();
		this.car_box = t;
		t.height = 405;
		t.width = 83;
		t.layout = this._VerticalLayout1_i();
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.gap = 2;
		t.horizontalAlign = "center";
		t.paddingTop = 5;
		t.verticalAlign = "top";
		return t;
	};
	_proto.newImg_i = function () {
		var t = new eui.Image();
		this.newImg = t;
		t.right = 0;
		t.source = "other_json.other2";
		t.top = 3;
		t.visible = false;
		return t;
	};
	_proto.txt_i = function () {
		var t = new eui.Group();
		this.txt = t;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.visible = false;
		t.layout = this._VerticalLayout2_i();
		t.elementsContent = [this._Label1_i(),this._Label2_i(),this._Label3_i(),this._Label4_i()];
		return t;
	};
	_proto._VerticalLayout2_i = function () {
		var t = new eui.VerticalLayout();
		t.gap = 20;
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.bold = true;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 50;
		t.text = "暂";
		t.textColor = 0x504fc1;
		t.verticalCenter = 0;
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.bold = true;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 50;
		t.text = "无";
		t.textColor = 0x504FC1;
		t.verticalCenter = 0;
		return t;
	};
	_proto._Label3_i = function () {
		var t = new eui.Label();
		t.bold = true;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 50;
		t.text = "记";
		t.textColor = 0x504FC1;
		t.verticalCenter = 0;
		return t;
	};
	_proto._Label4_i = function () {
		var t = new eui.Label();
		t.bold = true;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 50;
		t.text = "录";
		t.textColor = 0x504FC1;
		t.verticalCenter = 0;
		return t;
	};
	return LastOpenSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/GameUiSkin.exml'] = window.GameUiSkin = (function (_super) {
	__extends(GameUiSkin, _super);
	function GameUiSkin() {
		_super.call(this);
		this.skinParts = ["showTw","road","car","road2","signs","operate","operateList","show","show1","cutDown","caopan","table","recent","betGroup","setUp","balance","head","coupon"];
		
		this.height = 750;
		this.width = 1334;
		this.showTw_i();
		this.elementsContent = [this._Image1_i(),this.table_i(),this.recent_i(),this.betGroup_i(),this.head_i(),this._Group8_i()];
		
		eui.Binding.$bindProperties(this, ["show"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [0.5],[],this._Object1,"alpha")
		eui.Binding.$bindProperties(this, [0.8],[],this._Object1,"scaleX")
		eui.Binding.$bindProperties(this, [0.8],[],this._Object1,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object2,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object2,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object2,"scaleY")
		eui.Binding.$bindProperties(this, ["show1"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [0.5],[],this._Object3,"alpha")
		eui.Binding.$bindProperties(this, [0.8],[],this._Object3,"scaleX")
		eui.Binding.$bindProperties(this, [0.8],[],this._Object3,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object4,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object4,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object4,"scaleY")
	}
	var _proto = GameUiSkin.prototype;

	_proto.showTw_i = function () {
		var t = new egret.tween.TweenGroup();
		this.showTw = t;
		t.items = [this._TweenItem1_i(),this._TweenItem2_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Set1_i(),this._To1_i(),this._To2_i()];
		return t;
	};
	_proto._Set1_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._To1_i = function () {
		var t = new egret.tween.To();
		t.duration = 1000;
		t.props = this._Object1_i();
		return t;
	};
	_proto._Object1_i = function () {
		var t = {};
		this._Object1 = t;
		return t;
	};
	_proto._To2_i = function () {
		var t = new egret.tween.To();
		t.duration = 1000;
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto._TweenItem2_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem2 = t;
		t.paths = [this._Set2_i(),this._To3_i(),this._To4_i()];
		return t;
	};
	_proto._Set2_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._To3_i = function () {
		var t = new egret.tween.To();
		t.duration = 1000;
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto._To4_i = function () {
		var t = new egret.tween.To();
		t.duration = 1000;
		t.props = this._Object4_i();
		return t;
	};
	_proto._Object4_i = function () {
		var t = {};
		this._Object4 = t;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = -2;
		t.right = 2;
		t.source = "bg_game_png";
		t.top = 0;
		return t;
	};
	_proto.table_i = function () {
		var t = new eui.Group();
		this.table = t;
		t.bottom = 152;
		t.horizontalCenter = 0;
		t.top = 102;
		t.verticalCenter = -25;
		t.width = 1106;
		t.elementsContent = [this._Image2_i(),this.road_i(),this.car_i(),this.road2_i(),this.signs_i(),this._Group6_i()];
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 2;
		t.scaleY = 2;
		t.source = "enter_json.enter8";
		t.verticalCenter = 0;
		return t;
	};
	_proto.road_i = function () {
		var t = new eui.Image();
		this.road = t;
		t.horizontalCenter = 0;
		t.scaleX = 2;
		t.scaleY = 2;
		t.source = "enter_json.enter7";
		t.verticalCenter = 0;
		return t;
	};
	_proto.car_i = function () {
		var t = new eui.Image();
		this.car = t;
		t.anchorOffsetX = 33;
		t.anchorOffsetY = 16.5;
		t.source = "play_json.play34";
		t.x = 273.65;
		t.y = 116.54;
		return t;
	};
	_proto.road2_i = function () {
		var t = new eui.Image();
		this.road2 = t;
		t.horizontalCenter = 0;
		t.source = "bg_banker_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.signs_i = function () {
		var t = new eui.Group();
		this.signs = t;
		t.anchorOffsetY = 0;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.elementsContent = [this._CarLogo1_i(),this._CarLogo2_i(),this._CarLogo3_i(),this._CarLogo4_i(),this._CarLogo5_i(),this._CarLogo6_i(),this._CarLogo7_i(),this._CarLogo8_i(),this._CarLogo9_i(),this._CarLogo10_i(),this._CarLogo11_i(),this._CarLogo12_i(),this._CarLogo13_i(),this._CarLogo14_i(),this._CarLogo15_i(),this._CarLogo16_i(),this._CarLogo17_i(),this._CarLogo18_i(),this._CarLogo19_i(),this._CarLogo20_i(),this._CarLogo21_i(),this._CarLogo22_i(),this._CarLogo23_i(),this._CarLogo24_i(),this._CarLogo25_i(),this._CarLogo26_i(),this._CarLogo27_i(),this._CarLogo28_i(),this._CarLogo29_i(),this._CarLogo30_i()];
		return t;
	};
	_proto._CarLogo1_i = function () {
		var t = new CarLogo();
		t.left = 210;
		t.skinName = "CarLogoSkin";
		t.top = 0;
		return t;
	};
	_proto._CarLogo2_i = function () {
		var t = new CarLogo();
		t.left = 290;
		t.skinName = "CarLogoSkin";
		t.top = 0;
		return t;
	};
	_proto._CarLogo3_i = function () {
		var t = new CarLogo();
		t.left = 370;
		t.skinName = "CarLogoSkin";
		t.top = 0;
		return t;
	};
	_proto._CarLogo4_i = function () {
		var t = new CarLogo();
		t.left = 450;
		t.skinName = "CarLogoSkin";
		t.top = 0;
		return t;
	};
	_proto._CarLogo5_i = function () {
		var t = new CarLogo();
		t.left = 530;
		t.skinName = "CarLogoSkin";
		t.top = 0;
		return t;
	};
	_proto._CarLogo6_i = function () {
		var t = new CarLogo();
		t.left = 610;
		t.skinName = "CarLogoSkin";
		t.top = 0;
		return t;
	};
	_proto._CarLogo7_i = function () {
		var t = new CarLogo();
		t.left = 690;
		t.skinName = "CarLogoSkin";
		t.top = 0;
		return t;
	};
	_proto._CarLogo8_i = function () {
		var t = new CarLogo();
		t.left = 770;
		t.skinName = "CarLogoSkin";
		t.top = 0;
		return t;
	};
	_proto._CarLogo9_i = function () {
		var t = new CarLogo();
		t.right = 139;
		t.skinName = "CarLogoSkin";
		t.top = 10;
		t.width = 120;
		return t;
	};
	_proto._CarLogo10_i = function () {
		var t = new CarLogo();
		t.right = 67;
		t.skinName = "CarLogoSkin";
		t.top = 45;
		return t;
	};
	_proto._CarLogo11_i = function () {
		var t = new CarLogo();
		t.right = 20;
		t.skinName = "CarLogoSkin";
		t.top = 106;
		return t;
	};
	_proto._CarLogo12_i = function () {
		var t = new CarLogo();
		t.right = 2;
		t.skinName = "CarLogoSkin";
		t.top = 183;
		return t;
	};
	_proto._CarLogo13_i = function () {
		var t = new CarLogo();
		t.right = 14;
		t.skinName = "CarLogoSkin";
		t.top = 260;
		return t;
	};
	_proto._CarLogo14_i = function () {
		var t = new CarLogo();
		t.bottom = 52;
		t.right = 60;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo15_i = function () {
		var t = new CarLogo();
		t.bottom = 10;
		t.right = 127;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo16_i = function () {
		var t = new CarLogo();
		t.bottom = 0;
		t.height = 120;
		t.right = 206;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo17_i = function () {
		var t = new CarLogo();
		t.bottom = 0;
		t.right = 286;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo18_i = function () {
		var t = new CarLogo();
		t.bottom = 0;
		t.right = 366;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo19_i = function () {
		var t = new CarLogo();
		t.bottom = 0;
		t.right = 446;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo20_i = function () {
		var t = new CarLogo();
		t.bottom = 0;
		t.right = 526;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo21_i = function () {
		var t = new CarLogo();
		t.bottom = 0;
		t.right = 606;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo22_i = function () {
		var t = new CarLogo();
		t.bottom = 0;
		t.right = 686;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo23_i = function () {
		var t = new CarLogo();
		t.bottom = 0;
		t.right = 766;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo24_i = function () {
		var t = new CarLogo();
		t.bottom = 4;
		t.left = 139;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo25_i = function () {
		var t = new CarLogo();
		t.bottom = 41;
		t.left = 70;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo26_i = function () {
		var t = new CarLogo();
		t.bottom = 101;
		t.left = 23;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo27_i = function () {
		var t = new CarLogo();
		t.bottom = 178;
		t.left = 4;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo28_i = function () {
		var t = new CarLogo();
		t.bottom = 257;
		t.left = 19;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo29_i = function () {
		var t = new CarLogo();
		t.bottom = 321;
		t.left = 65;
		t.skinName = "CarLogoSkin";
		return t;
	};
	_proto._CarLogo30_i = function () {
		var t = new CarLogo();
		t.left = 131;
		t.skinName = "CarLogoSkin";
		t.top = 12;
		return t;
	};
	_proto._Group6_i = function () {
		var t = new eui.Group();
		t.height = 200;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 800;
		t.elementsContent = [this.operate_i(),this.operateList_i(),this._Group1_i(),this._Group2_i(),this.cutDown_i(),this._Group3_i(),this._Group4_i(),this._Group5_i()];
		return t;
	};
	_proto.operate_i = function () {
		var t = new eui.Image();
		this.operate = t;
		t.right = 80;
		t.source = "play_json.play30";
		t.top = 10;
		return t;
	};
	_proto.operateList_i = function () {
		var t = new eui.Image();
		this.operateList = t;
		t.right = 80;
		t.source = "play_json.play40";
		t.top = 75;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.height = 40;
		t.left = 80;
		t.width = 200;
		t.elementsContent = [this.show_i()];
		return t;
	};
	_proto.show_i = function () {
		var t = new eui.Image();
		this.show = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "play2_json.play3";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.height = 40;
		t.right = 80;
		t.width = 200;
		t.elementsContent = [this.show1_i()];
		return t;
	};
	_proto.show1_i = function () {
		var t = new eui.Image();
		this.show1 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "play2_json.play3";
		t.verticalCenter = 0;
		return t;
	};
	_proto.cutDown_i = function () {
		var t = new eui.BitmapLabel();
		this.cutDown = t;
		t.bottom = 10;
		t.font = "times_fnt";
		t.height = 35;
		t.horizontalCenter = 0;
		t.letterSpacing = 5;
		t.scaleX = 1.5;
		t.scaleY = 1.5;
		t.text = "00";
		t.textAlign = "center";
		t.verticalAlign = "bottom";
		t.width = 50;
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 104;
		t.left = 90;
		t.verticalCenter = -30;
		t.width = 100;
		t.elementsContent = [this._Image3_i(),this._Image4_i()];
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "play_json.play33";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "play_json.play58";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		t.left = 57;
		t.verticalCenter = -30;
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this._Image5_i(),this._Image6_i(),this._Image7_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.horizontalAlign = "center";
		t.verticalAlign = "top";
		return t;
	};
	_proto._Image5_i = function () {
		var t = new eui.Image();
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "play_json.play38";
		t.x = 14;
		t.y = 4;
		return t;
	};
	_proto._Image6_i = function () {
		var t = new eui.Image();
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "play_json.play43";
		t.x = 13;
		t.y = 39;
		return t;
	};
	_proto._Image7_i = function () {
		var t = new eui.Image();
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "play_json.play56";
		t.x = 14;
		t.y = 66;
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		t.height = 40;
		t.horizontalCenter = -80;
		t.verticalCenter = -30;
		t.width = 200;
		t.elementsContent = [this._Rect1_i(),this._Image8_i(),this.caopan_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 40;
		t.left = 0;
		t.right = 0;
		t.strokeColor = 0xe5bb69;
		t.strokeWeight = 3;
		t.top = 0;
		return t;
	};
	_proto._Image8_i = function () {
		var t = new eui.Image();
		t.left = 5;
		t.source = "play_json.play59";
		t.verticalCenter = 2;
		return t;
	};
	_proto.caopan_i = function () {
		var t = new eui.Label();
		this.caopan = t;
		t.bottom = 0;
		t.left = 40;
		t.right = 0;
		t.size = 25;
		t.text = "10000000";
		t.textAlign = "left";
		t.top = 0;
		t.verticalAlign = "middle";
		return t;
	};
	_proto.recent_i = function () {
		var t = new LastOpen();
		this.recent = t;
		t.height = 446;
		t.left = 0;
		t.skinName = "LastOpenSkin";
		t.verticalCenter = 0;
		t.width = 83;
		return t;
	};
	_proto.betGroup_i = function () {
		var t = new BetGroup();
		this.betGroup = t;
		t.bottom = 0;
		t.height = 146;
		t.horizontalCenter = 0;
		t.left = 0;
		t.right = 0;
		t.skinName = "BetGroupSkin";
		return t;
	};
	_proto.head_i = function () {
		var t = new eui.Group();
		this.head = t;
		t.height = 125;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.verticalCenter = -312.5;
		t.elementsContent = [this._Image9_i(),this.setUp_i(),this._Image10_i(),this._Group7_i()];
		return t;
	};
	_proto._Image9_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "bg_header_png";
		t.top = 0;
		return t;
	};
	_proto.setUp_i = function () {
		var t = new eui.Image();
		this.setUp = t;
		t.right = 40;
		t.scaleX = 1.5;
		t.scaleY = 1.5;
		t.source = "header_json.header5";
		t.top = 20;
		return t;
	};
	_proto._Image10_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "header_json.header1";
		t.top = 20;
		t.x = 531;
		t.y = 20;
		return t;
	};
	_proto._Group7_i = function () {
		var t = new eui.Group();
		t.height = 50;
		t.horizontalCenter = -342;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 20;
		t.width = 250;
		t.elementsContent = [this._Rect2_i(),this._Image11_i(),this._Image12_i(),this.balance_i()];
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 50;
		t.fillColor = 0x7e1ca5;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Image11_i = function () {
		var t = new eui.Image();
		t.left = 0;
		t.source = "header_json.header6";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image12_i = function () {
		var t = new eui.Image();
		t.right = 0;
		t.source = "header_json.header9";
		t.verticalCenter = 0;
		return t;
	};
	_proto.balance_i = function () {
		var t = new eui.Label();
		this.balance = t;
		t.bottom = 5;
		t.left = 40;
		t.right = 40;
		t.text = "66666666";
		t.textAlign = "center";
		t.textColor = 0xefcd81;
		t.top = 5;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Group8_i = function () {
		var t = new eui.Group();
		t.height = 60;
		t.visible = false;
		t.width = 120;
		t.x = 436;
		t.y = 600;
		t.elementsContent = [this._Rect3_i(),this.coupon_i()];
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 20;
		t.fillColor = 0xb040dd;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto.coupon_i = function () {
		var t = new eui.Label();
		this.coupon = t;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.text = "免费X2\n额度200";
		t.textAlign = "center";
		t.textColor = 0xffc64c;
		t.top = 0;
		t.verticalAlign = "middle";
		return t;
	};
	return GameUiSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/GirlSkin.exml'] = window.GirlSkin = (function (_super) {
	__extends(GirlSkin, _super);
	function GirlSkin() {
		_super.call(this);
		this.skinParts = ["animation","times","hands","clothes","Hair","three","two","one","go0"];
		
		this.height = 750;
		this.width = 1334;
		this.animation_i();
		this.times_i();
		this.elementsContent = [this._Rect1_i(),this._Group1_i(),this._Group3_i()];
		
		eui.Binding.$bindProperties(this, ["Hair"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [-10],[],this._Object1,"rotation")
		eui.Binding.$bindProperties(this, [0],[],this._Object2,"rotation")
		eui.Binding.$bindProperties(this, [-5],[],this._Object3,"rotation")
		eui.Binding.$bindProperties(this, ["hands"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object4,"rotation")
		eui.Binding.$bindProperties(this, [5],[],this._Object5,"rotation")
		eui.Binding.$bindProperties(this, [0],[],this._Object6,"rotation")
		eui.Binding.$bindProperties(this, ["three"],[0],this._TweenItem3,"target")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object7,"scaleX")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object7,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object8,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object8,"scaleX")
		eui.Binding.$bindProperties(this, [0],[],this._Object8,"scaleY")
		eui.Binding.$bindProperties(this, ["two"],[0],this._TweenItem4,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object9,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object10,"alpha")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object10,"scaleX")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object10,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object11,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object11,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object11,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object12,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object12,"scaleX")
		eui.Binding.$bindProperties(this, [0],[],this._Object12,"scaleY")
		eui.Binding.$bindProperties(this, ["one"],[0],this._TweenItem5,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object13,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object14,"alpha")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object14,"scaleX")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object14,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object15,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object15,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object15,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object16,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object16,"scaleX")
		eui.Binding.$bindProperties(this, [0],[],this._Object16,"scaleY")
		eui.Binding.$bindProperties(this, ["go0"],[0],this._TweenItem6,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object17,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object18,"alpha")
		eui.Binding.$bindProperties(this, [3],[],this._Object18,"scaleX")
		eui.Binding.$bindProperties(this, [3],[],this._Object18,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object19,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object19,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object19,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object20,"alpha")
		eui.Binding.$bindProperties(this, [2],[],this._Object20,"scaleX")
		eui.Binding.$bindProperties(this, [2],[],this._Object20,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object21,"alpha")
		eui.Binding.$bindProperties(this, [3],[],this._Object21,"scaleX")
		eui.Binding.$bindProperties(this, [3],[],this._Object21,"scaleY")
	}
	var _proto = GirlSkin.prototype;

	_proto.animation_i = function () {
		var t = new egret.tween.TweenGroup();
		this.animation = t;
		t.items = [this._TweenItem1_i(),this._TweenItem2_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Set1_i(),this._To1_i(),this._To2_i()];
		return t;
	};
	_proto._Set1_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object1_i();
		return t;
	};
	_proto._Object1_i = function () {
		var t = {};
		this._Object1 = t;
		return t;
	};
	_proto._To1_i = function () {
		var t = new egret.tween.To();
		t.duration = 2000;
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto._To2_i = function () {
		var t = new egret.tween.To();
		t.duration = 2000;
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto._TweenItem2_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem2 = t;
		t.paths = [this._Set2_i(),this._To3_i(),this._To4_i()];
		return t;
	};
	_proto._Set2_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object4_i();
		return t;
	};
	_proto._Object4_i = function () {
		var t = {};
		this._Object4 = t;
		return t;
	};
	_proto._To3_i = function () {
		var t = new egret.tween.To();
		t.duration = 2000;
		t.props = this._Object5_i();
		return t;
	};
	_proto._Object5_i = function () {
		var t = {};
		this._Object5 = t;
		return t;
	};
	_proto._To4_i = function () {
		var t = new egret.tween.To();
		t.duration = 2000;
		t.ease = "sineOut";
		t.props = this._Object6_i();
		return t;
	};
	_proto._Object6_i = function () {
		var t = {};
		this._Object6 = t;
		return t;
	};
	_proto.times_i = function () {
		var t = new egret.tween.TweenGroup();
		this.times = t;
		t.items = [this._TweenItem3_i(),this._TweenItem4_i(),this._TweenItem5_i(),this._TweenItem6_i()];
		return t;
	};
	_proto._TweenItem3_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem3 = t;
		t.paths = [this._Set3_i(),this._To5_i(),this._To6_i()];
		return t;
	};
	_proto._Set3_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._To5_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object7_i();
		return t;
	};
	_proto._Object7_i = function () {
		var t = {};
		this._Object7 = t;
		return t;
	};
	_proto._To6_i = function () {
		var t = new egret.tween.To();
		t.duration = 750;
		t.props = this._Object8_i();
		return t;
	};
	_proto._Object8_i = function () {
		var t = {};
		this._Object8 = t;
		return t;
	};
	_proto._TweenItem4_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem4 = t;
		t.paths = [this._Set4_i(),this._Wait1_i(),this._Set5_i(),this._To7_i(),this._To8_i()];
		return t;
	};
	_proto._Set4_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object9_i();
		return t;
	};
	_proto._Object9_i = function () {
		var t = {};
		this._Object9 = t;
		return t;
	};
	_proto._Wait1_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 1000;
		return t;
	};
	_proto._Set5_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object10_i();
		return t;
	};
	_proto._Object10_i = function () {
		var t = {};
		this._Object10 = t;
		return t;
	};
	_proto._To7_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object11_i();
		return t;
	};
	_proto._Object11_i = function () {
		var t = {};
		this._Object11 = t;
		return t;
	};
	_proto._To8_i = function () {
		var t = new egret.tween.To();
		t.duration = 750;
		t.props = this._Object12_i();
		return t;
	};
	_proto._Object12_i = function () {
		var t = {};
		this._Object12 = t;
		return t;
	};
	_proto._TweenItem5_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem5 = t;
		t.paths = [this._Set6_i(),this._Wait2_i(),this._Set7_i(),this._To9_i(),this._To10_i()];
		return t;
	};
	_proto._Set6_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object13_i();
		return t;
	};
	_proto._Object13_i = function () {
		var t = {};
		this._Object13 = t;
		return t;
	};
	_proto._Wait2_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 2000;
		return t;
	};
	_proto._Set7_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object14_i();
		return t;
	};
	_proto._Object14_i = function () {
		var t = {};
		this._Object14 = t;
		return t;
	};
	_proto._To9_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object15_i();
		return t;
	};
	_proto._Object15_i = function () {
		var t = {};
		this._Object15 = t;
		return t;
	};
	_proto._To10_i = function () {
		var t = new egret.tween.To();
		t.duration = 750;
		t.props = this._Object16_i();
		return t;
	};
	_proto._Object16_i = function () {
		var t = {};
		this._Object16 = t;
		return t;
	};
	_proto._TweenItem6_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem6 = t;
		t.paths = [this._Set8_i(),this._Wait3_i(),this._Set9_i(),this._To11_i(),this._To12_i(),this._To13_i()];
		return t;
	};
	_proto._Set8_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object17_i();
		return t;
	};
	_proto._Object17_i = function () {
		var t = {};
		this._Object17 = t;
		return t;
	};
	_proto._Wait3_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 3000;
		return t;
	};
	_proto._Set9_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object18_i();
		return t;
	};
	_proto._Object18_i = function () {
		var t = {};
		this._Object18 = t;
		return t;
	};
	_proto._To11_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object19_i();
		return t;
	};
	_proto._Object19_i = function () {
		var t = {};
		this._Object19 = t;
		return t;
	};
	_proto._To12_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object20_i();
		return t;
	};
	_proto._Object20_i = function () {
		var t = {};
		this._Object20 = t;
		return t;
	};
	_proto._To13_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object21_i();
		return t;
	};
	_proto._Object21_i = function () {
		var t = {};
		this._Object21 = t;
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.bottom = -100;
		t.left = 200;
		t.elementsContent = [this._Image1_i(),this._Image2_i(),this.hands_i(),this._Image3_i(),this._Image4_i(),this.clothes_i(),this._Image5_i(),this._Image6_i(),this._Image7_i(),this._Image8_i(),this._Image9_i(),this._Image10_i(),this._Image11_i(),this._Image12_i(),this.Hair_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl4";
		t.x = 195;
		t.y = 225;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl27";
		t.x = -34.5;
		t.y = 87.96;
		return t;
	};
	_proto.hands_i = function () {
		var t = new eui.Image();
		this.hands = t;
		t.anchorOffsetX = 77;
		t.anchorOffsetY = 214;
		t.left = 6;
		t.source = "girl_json.girl23";
		t.y = 315.98;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl21";
		t.x = 22;
		t.y = -4;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl3";
		t.x = 190;
		t.y = 161;
		return t;
	};
	_proto.clothes_i = function () {
		var t = new eui.Image();
		this.clothes = t;
		t.source = "girl_json.girl1";
		t.x = 186.67;
		t.y = 140.02;
		return t;
	};
	_proto._Image5_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl12";
		t.x = 197.68;
		t.y = 146.72;
		return t;
	};
	_proto._Image6_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl15";
		t.x = 187.03;
		t.y = 151.98;
		return t;
	};
	_proto._Image7_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl14";
		t.x = 96.19;
		t.y = -7.96;
		return t;
	};
	_proto._Image8_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl6";
		t.x = 50.96;
		t.y = 330.68;
		return t;
	};
	_proto._Image9_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl22";
		t.x = 128;
		t.y = 382.3;
		return t;
	};
	_proto._Image10_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl9";
		t.x = 49.96;
		t.y = 333.96;
		return t;
	};
	_proto._Image11_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl10";
		t.x = 149.29;
		t.y = 220.48;
		return t;
	};
	_proto._Image12_i = function () {
		var t = new eui.Image();
		t.source = "girl_json.girl7";
		t.x = 157.63;
		t.y = 148.95;
		return t;
	};
	_proto.Hair_i = function () {
		var t = new eui.Image();
		this.Hair = t;
		t.anchorOffsetX = 0.99;
		t.anchorOffsetY = 0.17;
		t.rotation = -10;
		t.source = "girl_json.girl0";
		t.x = 169.68;
		t.y = 125;
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.height = 200;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 200;
		t.elementsContent = [this._Group2_i()];
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.elementsContent = [this.three_i(),this.two_i(),this.one_i(),this.go0_i()];
		return t;
	};
	_proto.three_i = function () {
		var t = new eui.Image();
		this.three = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "time_json.time7";
		t.verticalCenter = 0;
		return t;
	};
	_proto.two_i = function () {
		var t = new eui.Image();
		this.two = t;
		t.horizontalCenter = 0;
		t.source = "time_json.time8";
		t.verticalCenter = 0;
		return t;
	};
	_proto.one_i = function () {
		var t = new eui.Image();
		this.one = t;
		t.horizontalCenter = 0;
		t.source = "time_json.time9";
		t.verticalCenter = 0;
		return t;
	};
	_proto.go0_i = function () {
		var t = new eui.Image();
		this.go0 = t;
		t.horizontalCenter = 0;
		t.source = "time_json.time0";
		t.verticalCenter = 0;
		return t;
	};
	return GirlSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/HelpSkin.exml'] = window.HelpSkin = (function (_super) {
	__extends(HelpSkin, _super);
	function HelpSkin() {
		_super.call(this);
		this.skinParts = ["back","logo"];
		
		this.height = 750;
		this.width = 1334;
		this.elementsContent = [this.back_i(),this._Group7_i(),this.logo_i()];
	}
	var _proto = HelpSkin.prototype;

	_proto.back_i = function () {
		var t = new eui.Rect();
		this.back = t;
		t.bottom = 0;
		t.fillAlpha = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group7_i = function () {
		var t = new eui.Group();
		t.height = 550;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 900;
		t.elementsContent = [this._Rect1_i(),this._Rect2_i(),this._Scroller1_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 100;
		t.fillColor = 0x3f4fa8;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.ellipseWidth = 100;
		t.fillColor = 0x1b2244;
		t.left = 10;
		t.right = 10;
		t.top = 10;
		return t;
	};
	_proto._Scroller1_i = function () {
		var t = new eui.Scroller();
		t.anchorOffsetY = 0;
		t.height = 440;
		t.horizontalCenter = 0;
		t.top = 70;
		t.width = 840;
		t.viewport = this._Group6_i();
		return t;
	};
	_proto._Group6_i = function () {
		var t = new eui.Group();
		t.layout = this._VerticalLayout2_i();
		t.elementsContent = [this._Group4_i(),this._Group5_i()];
		return t;
	};
	_proto._VerticalLayout2_i = function () {
		var t = new eui.VerticalLayout();
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 364;
		t.percentWidth = 100;
		t.x = 0;
		t.y = 370;
		t.elementsContent = [this._Label1_i(),this._Group1_i(),this._Group3_i()];
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.left = 0;
		t.size = 50;
		t.text = "玩法说明：";
		t.textColor = 0x5c669a;
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.left = 50;
		t.top = 80;
		t.elementsContent = [this._Image1_i(),this._Image2_i(),this._Image3_i(),this._Image4_i(),this._Image5_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.source = "help1_json.help18";
		t.x = 172.5;
		t.y = -8;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.left = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "help1_json.help15";
		t.top = 0;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.source = "help1_json.help17";
		t.x = 163;
		t.y = 68;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "help1_json.help19";
		t.x = 169;
		t.y = 144;
		return t;
	};
	_proto._Image5_i = function () {
		var t = new eui.Image();
		t.source = "help1_json.help16";
		t.x = 167;
		t.y = 116;
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.right = 50;
		t.top = 80;
		t.elementsContent = [this._Image6_i(),this._Image7_i(),this._Image8_i(),this._Group2_i()];
		return t;
	};
	_proto._Image6_i = function () {
		var t = new eui.Image();
		t.left = 0;
		t.source = "help1_json.help14";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image7_i = function () {
		var t = new eui.Image();
		t.source = "help1_json.help110";
		t.x = 143;
		t.y = 12;
		return t;
	};
	_proto._Image8_i = function () {
		var t = new eui.Image();
		t.source = "help1_json.help111";
		t.x = 146;
		t.y = 40;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.x = 144;
		t.y = 64;
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this._Label2_i(),this._Label3_i(),this._Label4_i(),this._Label5_i(),this._Label6_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.horizontalAlign = "left";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.text = "100";
		t.textColor = 0x5c669a;
		t.x = 31;
		t.y = 36;
		return t;
	};
	_proto._Label3_i = function () {
		var t = new eui.Label();
		t.text = "500";
		t.textColor = 0x5c669a;
		t.x = 41;
		t.y = 46;
		return t;
	};
	_proto._Label4_i = function () {
		var t = new eui.Label();
		t.text = "1000";
		t.textColor = 0x5c669a;
		t.x = 51;
		t.y = 56;
		return t;
	};
	_proto._Label5_i = function () {
		var t = new eui.Label();
		t.text = "2000";
		t.textColor = 0x5c669a;
		t.x = 61;
		t.y = 66;
		return t;
	};
	_proto._Label6_i = function () {
		var t = new eui.Label();
		t.text = "5000";
		t.textColor = 0x5c669a;
		t.x = 71;
		t.y = 76;
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		t.horizontalCenter = 0;
		t.top = 0;
		t.percentWidth = 100;
		t.elementsContent = [this._Image9_i(),this._Image10_i(),this._Label7_i()];
		return t;
	};
	_proto._Image9_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "help271_png";
		t.top = 80;
		return t;
	};
	_proto._Image10_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "help272_png";
		t.top = 140;
		return t;
	};
	_proto._Label7_i = function () {
		var t = new eui.Label();
		t.left = 0;
		t.size = 50;
		t.text = "赔率说明：";
		t.textColor = 0x5c669a;
		t.top = 0;
		return t;
	};
	_proto.logo_i = function () {
		var t = new eui.Group();
		this.logo = t;
		t.height = 100;
		t.horizontalCenter = 0;
		t.top = 54;
		t.width = 380;
		t.elementsContent = [this._Rect3_i(),this._Label8_i()];
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 50;
		t.fillColor = 0x151516;
		t.left = 0;
		t.right = 0;
		t.strokeAlpha = 1;
		t.strokeColor = 0x3f4fa8;
		t.strokeWeight = 10;
		t.top = 0;
		return t;
	};
	_proto._Label8_i = function () {
		var t = new eui.Label();
		t.bold = true;
		t.horizontalCenter = 0;
		t.size = 50;
		t.text = "帮助";
		t.textColor = 0xa0a6e5;
		t.verticalCenter = 0;
		return t;
	};
	return HelpSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/HisItemSkin.exml'] = window.HisItemSkin = (function (_super) {
	__extends(HisItemSkin, _super);
	function HisItemSkin() {
		_super.call(this);
		this.skinParts = ["line","create_time","issue","bouns","code"];
		
		this.width = 840;
		this.elementsContent = [this._Rect1_i(),this.line_i(),this.create_time_i(),this._Group1_i(),this.bouns_i(),this.code_i()];
	}
	var _proto = HisItemSkin.prototype;

	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 20;
		t.fillColor = 0x3546c4;
		t.left = 0;
		t.right = 0;
		t.strokeColor = 0x2fbc4f;
		t.strokeWeight = 5;
		t.top = 0;
		t.visible = false;
		return t;
	};
	_proto.line_i = function () {
		var t = new eui.Group();
		this.line = t;
		t.bottom = 0;
		t.height = 3;
		t.horizontalCenter = 0;
		t.width = 840;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this._Rect2_i(),this._Rect3_i(),this._Rect4_i(),this._Rect5_i(),this._Rect6_i(),this._Rect7_i(),this._Rect8_i(),this._Rect9_i(),this._Rect10_i(),this._Rect11_i(),this._Rect12_i(),this._Rect13_i(),this._Rect14_i(),this._Rect15_i(),this._Rect16_i(),this._Rect17_i(),this._Rect18_i(),this._Rect19_i(),this._Rect20_i(),this._Rect21_i(),this._Rect22_i(),this._Rect23_i(),this._Rect24_i(),this._Rect25_i(),this._Rect26_i(),this._Rect27_i(),this._Rect28_i(),this._Rect29_i(),this._Rect30_i(),this._Rect31_i(),this._Rect32_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 50;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 60;
		t.y = 10;
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 70;
		t.y = 20;
		return t;
	};
	_proto._Rect5_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 80;
		t.y = 30;
		return t;
	};
	_proto._Rect6_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 90;
		t.y = 40;
		return t;
	};
	_proto._Rect7_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 100;
		t.y = 50;
		return t;
	};
	_proto._Rect8_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 110;
		t.y = 60;
		return t;
	};
	_proto._Rect9_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 120;
		t.y = 70;
		return t;
	};
	_proto._Rect10_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 130;
		t.y = 80;
		return t;
	};
	_proto._Rect11_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 140;
		t.y = 90;
		return t;
	};
	_proto._Rect12_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 60;
		t.y = 10;
		return t;
	};
	_proto._Rect13_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 70;
		t.y = 20;
		return t;
	};
	_proto._Rect14_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 80;
		t.y = 30;
		return t;
	};
	_proto._Rect15_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 90;
		t.y = 40;
		return t;
	};
	_proto._Rect16_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 80;
		return t;
	};
	_proto._Rect17_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 110;
		return t;
	};
	_proto._Rect18_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 140;
		return t;
	};
	_proto._Rect19_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 170;
		return t;
	};
	_proto._Rect20_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 200;
		return t;
	};
	_proto._Rect21_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 230;
		return t;
	};
	_proto._Rect22_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 260;
		return t;
	};
	_proto._Rect23_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 290;
		return t;
	};
	_proto._Rect24_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 320;
		return t;
	};
	_proto._Rect25_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 350;
		return t;
	};
	_proto._Rect26_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 380;
		return t;
	};
	_proto._Rect27_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 410;
		return t;
	};
	_proto._Rect28_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 440;
		return t;
	};
	_proto._Rect29_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 470;
		return t;
	};
	_proto._Rect30_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 500;
		return t;
	};
	_proto._Rect31_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 530;
		return t;
	};
	_proto._Rect32_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 560;
		return t;
	};
	_proto.create_time_i = function () {
		var t = new eui.Label();
		this.create_time = t;
		t.height = 30;
		t.left = 20;
		t.size = 25;
		t.text = "2018-04-12 09:54:44";
		t.top = 10;
		t.verticalAlign = "middle";
		t.width = 280;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.right = 20;
		t.top = 10;
		t.layout = this._HorizontalLayout2_i();
		t.elementsContent = [this._Label1_i(),this.issue_i()];
		return t;
	};
	_proto._HorizontalLayout2_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "left";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.height = 30;
		t.right = 40;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 25;
		t.text = "期号：";
		t.top = 20;
		t.verticalAlign = "middle";
		t.x = 12;
		t.y = 12;
		return t;
	};
	_proto.issue_i = function () {
		var t = new eui.Label();
		this.issue = t;
		t.height = 30;
		t.right = 20;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 25;
		t.text = "201804120950";
		t.textAlign = "right";
		t.top = 20;
		t.verticalAlign = "middle";
		t.x = 32;
		t.y = 12;
		return t;
	};
	_proto.bouns_i = function () {
		var t = new eui.Label();
		this.bouns = t;
		t.right = 20;
		t.text = "获得1000000金豆";
		t.textAlign = "right";
		t.textColor = 0xffffff;
		t.verticalCenter = 20;
		t.width = 300;
		return t;
	};
	_proto.code_i = function () {
		var t = new eui.Label();
		this.code = t;
		t.left = 20;
		t.size = 25;
		t.text = "法拉利：100金豆";
		t.top = 50;
		t.width = 280;
		return t;
	};
	return HisItemSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/HistorySkin.exml'] = window.HistorySkin = (function (_super) {
	__extends(HistorySkin, _super);
	function HistorySkin() {
		_super.call(this);
		this.skinParts = ["back","zero","hisList","loading","hisScroller","logo"];
		
		this.height = 750;
		this.width = 1334;
		this.elementsContent = [this.back_i(),this._Group2_i(),this.logo_i()];
	}
	var _proto = HistorySkin.prototype;

	_proto.back_i = function () {
		var t = new eui.Rect();
		this.back = t;
		t.bottom = 0;
		t.fillAlpha = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.height = 550;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 900;
		t.elementsContent = [this._Rect1_i(),this._Rect2_i(),this.zero_i(),this._Rect3_i(),this.hisScroller_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 100;
		t.fillColor = 0x3f4fa8;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.ellipseWidth = 100;
		t.fillColor = 0x1b2244;
		t.left = 10;
		t.right = 10;
		t.top = 10;
		return t;
	};
	_proto.zero_i = function () {
		var t = new eui.Label();
		this.zero = t;
		t.horizontalCenter = 0;
		t.text = "暂无记录！";
		t.verticalCenter = 0;
		t.visible = false;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x1cbc33;
		t.height = 5;
		t.horizontalCenter = 0;
		t.top = 65;
		t.width = 800;
		return t;
	};
	_proto.hisScroller_i = function () {
		var t = new eui.Scroller();
		this.hisScroller = t;
		t.anchorOffsetY = 0;
		t.bottom = 40;
		t.horizontalCenter = 0;
		t.top = 70;
		t.width = 840;
		t.viewport = this._Group1_i();
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this.hisList_i(),this.loading_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.horizontalAlign = "center";
		return t;
	};
	_proto.hisList_i = function () {
		var t = new eui.List();
		this.hisList = t;
		t.horizontalCenter = 0;
		t.itemRendererSkinName = HisItemSkin;
		t.top = 0;
		t.percentWidth = 100;
		return t;
	};
	_proto.loading_i = function () {
		var t = new eui.Label();
		this.loading = t;
		t.background = true;
		t.backgroundColor = 0xc1aeae;
		t.bottom = 0;
		t.height = 35;
		t.horizontalCenter = 0;
		t.size = 25;
		t.text = "上拉获取更多";
		t.textAlign = "center";
		t.verticalAlign = "middle";
		t.visible = false;
		t.width = 800;
		return t;
	};
	_proto.logo_i = function () {
		var t = new eui.Group();
		this.logo = t;
		t.height = 100;
		t.horizontalCenter = 0;
		t.top = 54;
		t.width = 380;
		t.elementsContent = [this._Rect4_i(),this._Label1_i()];
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 50;
		t.fillColor = 0x151516;
		t.left = 0;
		t.right = 0;
		t.strokeAlpha = 1;
		t.strokeColor = 0x3f4fa8;
		t.strokeWeight = 10;
		t.top = 0;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.bold = true;
		t.horizontalCenter = 0;
		t.size = 50;
		t.text = "历史记录";
		t.textColor = 0xa0a6e5;
		t.verticalCenter = 0;
		return t;
	};
	return HistorySkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/ItemSkin.exml'] = window.ItemSkin = (function (_super) {
	__extends(ItemSkin, _super);
	function ItemSkin() {
		_super.call(this);
		this.skinParts = ["txt"];
		
		this.elementsContent = [this._Image1_i(),this.txt_i()];
		this.states = [
			new eui.State ("up",
				[
				])
			,
			new eui.State ("down",
				[
					new eui.SetProperty("_Image1","alpha",2),
					new eui.SetProperty("txt","alpha",2)
				])
			,
			new eui.State ("disabled",
				[
				])
		];
	}
	var _proto = ItemSkin.prototype;

	_proto._Image1_i = function () {
		var t = new eui.Image();
		this._Image1 = t;
		t.horizontalCenter = 0;
		t.source = "bet_json.bet24";
		t.verticalCenter = 0;
		return t;
	};
	_proto.txt_i = function () {
		var t = new eui.Label();
		this.txt = t;
		t.bottom = 5;
		t.left = 8;
		t.right = 8;
		t.text = "100";
		t.textAlign = "center";
		t.textColor = 0x3099e8;
		t.top = 10;
		t.verticalAlign = "middle";
		return t;
	};
	return ItemSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/LoadingSkin.exml'] = window.LoadingSkin = (function (_super) {
	__extends(LoadingSkin, _super);
	function LoadingSkin() {
		_super.call(this);
		this.skinParts = ["load","loading","light1","light2"];
		
		this.height = 750;
		this.width = 1334;
		this.load_i();
		this.elementsContent = [this._Image1_i(),this._Image2_i(),this._Image3_i(),this.loading_i(),this.light1_i(),this.light2_i()];
		
		eui.Binding.$bindProperties(this, ["light1"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [3],[],this._Object1,"scaleX")
		eui.Binding.$bindProperties(this, [3],[],this._Object1,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object2,"alpha")
		eui.Binding.$bindProperties(this, ["light2"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [3],[],this._Object3,"scaleX")
		eui.Binding.$bindProperties(this, [3],[],this._Object3,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object4,"alpha")
	}
	var _proto = LoadingSkin.prototype;

	_proto.load_i = function () {
		var t = new egret.tween.TweenGroup();
		this.load = t;
		t.items = [this._TweenItem1_i(),this._TweenItem2_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Set1_i(),this._To1_i(),this._To2_i()];
		return t;
	};
	_proto._Set1_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._To1_i = function () {
		var t = new egret.tween.To();
		t.duration = 1000;
		t.props = this._Object1_i();
		return t;
	};
	_proto._Object1_i = function () {
		var t = {};
		this._Object1 = t;
		return t;
	};
	_proto._To2_i = function () {
		var t = new egret.tween.To();
		t.duration = 500;
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto._TweenItem2_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem2 = t;
		t.paths = [this._Set2_i(),this._To3_i(),this._To4_i()];
		return t;
	};
	_proto._Set2_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._To3_i = function () {
		var t = new egret.tween.To();
		t.duration = 1000;
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto._To4_i = function () {
		var t = new egret.tween.To();
		t.duration = 500;
		t.props = this._Object4_i();
		return t;
	};
	_proto._Object4_i = function () {
		var t = {};
		this._Object4 = t;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "start_ani (1)_json.start_ani (1)4";
		t.top = 0;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 2;
		t.scaleY = 2;
		t.source = "start_ani (1)_json.start_ani (1)1";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.left = 96;
		t.source = "start_ani (1)_json.start_ani (1)9";
		t.top = 83;
		return t;
	};
	_proto.loading_i = function () {
		var t = new eui.Label();
		this.loading = t;
		t.bottom = 80;
		t.horizontalCenter = 0;
		t.text = "正在加载中。。。0%";
		return t;
	};
	_proto.light1_i = function () {
		var t = new eui.Image();
		this.light1 = t;
		t.horizontalCenter = -400;
		t.source = "start_ani (1)_json.start_ani (1)13";
		t.verticalCenter = 0;
		return t;
	};
	_proto.light2_i = function () {
		var t = new eui.Image();
		this.light2 = t;
		t.horizontalCenter = 400;
		t.source = "start_ani (1)_json.start_ani (1)14";
		t.verticalCenter = 0;
		return t;
	};
	return LoadingSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/TextInputSkin.exml'] = window.TextInputSkin = (function (_super) {
	__extends(TextInputSkin, _super);
	function TextInputSkin() {
		_super.call(this);
		this.skinParts = ["textDisplay","promptDisplay"];
		
		this.height = 50;
		this.width = 200;
		this.elementsContent = [this._Rect1_i(),this.textDisplay_i()];
		this.promptDisplay_i();
		
		this.states = [
			new eui.State ("normal",
				[
					new eui.SetProperty("textDisplay","textAlign","center"),
					new eui.SetProperty("","width",200)
				])
			,
			new eui.State ("disabled",
				[
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

	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 20;
		t.fillColor = 0x1b2244;
		t.left = 0;
		t.right = 0;
		t.strokeColor = 0x3f4fa8;
		t.strokeWeight = 3;
		t.top = 0;
		return t;
	};
	_proto.textDisplay_i = function () {
		var t = new eui.EditableText();
		this.textDisplay = t;
		t.bottom = "8";
		t.left = "8";
		t.right = "8";
		t.size = 30;
		t.text = "";
		t.textColor = 0xcc7f1e;
		t.top = "8";
		t.verticalAlign = "middle";
		return t;
	};
	_proto.promptDisplay_i = function () {
		var t = new eui.Label();
		this.promptDisplay = t;
		t.bottom = 8;
		t.left = 8;
		t.right = 8;
		t.text = "";
		t.textAlign = "center";
		t.textColor = 0xcc7f1e;
		t.top = 8;
		t.verticalAlign = "middle";
		return t;
	};
	return TextInputSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/OperatePanelSkin.exml'] = window.OperatePanelSkin = (function (_super) {
	__extends(OperatePanelSkin, _super);
	var OperatePanelSkin$Skin5 = 	(function (_super) {
		__extends(OperatePanelSkin$Skin5, _super);
		function OperatePanelSkin$Skin5() {
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
						new eui.SetProperty("_Image1","source","operate_setup_json.operate_setup7")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = OperatePanelSkin$Skin5.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "operate_setup_json.operate_setup6";
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
		return OperatePanelSkin$Skin5;
	})(eui.Skin);

	var OperatePanelSkin$Skin6 = 	(function (_super) {
		__extends(OperatePanelSkin$Skin6, _super);
		function OperatePanelSkin$Skin6() {
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
						new eui.SetProperty("_Image1","source","operate_setup_json.operate_setup7")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = OperatePanelSkin$Skin6.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "operate_setup_json.operate_setup6";
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
		return OperatePanelSkin$Skin6;
	})(eui.Skin);

	var OperatePanelSkin$Skin7 = 	(function (_super) {
		__extends(OperatePanelSkin$Skin7, _super);
		function OperatePanelSkin$Skin7() {
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
						new eui.SetProperty("_Image1","source","operate_setup_json.operate_setup7")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = OperatePanelSkin$Skin7.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "operate_setup_json.operate_setup6";
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
		return OperatePanelSkin$Skin7;
	})(eui.Skin);

	function OperatePanelSkin() {
		_super.call(this);
		this.skinParts = ["home","open","back","caoMoney","auto","setPan","cancel","sure","question","logo","group"];
		
		this.height = 750;
		this.width = 1334;
		this.home_i();
		this.open_i();
		this.elementsContent = [this.back_i(),this.group_i()];
		
		eui.Binding.$bindProperties(this, ["group"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [1],[],this._Object1,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object1,"scaleY")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object2,"scaleX")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object2,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object3,"scaleX")
		eui.Binding.$bindProperties(this, [0],[],this._Object3,"scaleY")
		eui.Binding.$bindProperties(this, ["group"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object4,"scaleX")
		eui.Binding.$bindProperties(this, [0],[],this._Object4,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object5,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object5,"scaleY")
	}
	var _proto = OperatePanelSkin.prototype;

	_proto.home_i = function () {
		var t = new egret.tween.TweenGroup();
		this.home = t;
		t.items = [this._TweenItem1_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Set1_i(),this._To1_i(),this._To2_i()];
		return t;
	};
	_proto._Set1_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object1_i();
		return t;
	};
	_proto._Object1_i = function () {
		var t = {};
		this._Object1 = t;
		return t;
	};
	_proto._To1_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto._To2_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto.open_i = function () {
		var t = new egret.tween.TweenGroup();
		this.open = t;
		t.items = [this._TweenItem2_i()];
		return t;
	};
	_proto._TweenItem2_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem2 = t;
		t.paths = [this._Set2_i(),this._To3_i()];
		return t;
	};
	_proto._Set2_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object4_i();
		return t;
	};
	_proto._Object4_i = function () {
		var t = {};
		this._Object4 = t;
		return t;
	};
	_proto._To3_i = function () {
		var t = new egret.tween.To();
		t.duration = 350;
		t.props = this._Object5_i();
		return t;
	};
	_proto._Object5_i = function () {
		var t = {};
		this._Object5 = t;
		return t;
	};
	_proto.back_i = function () {
		var t = new eui.Rect();
		this.back = t;
		t.bottom = 0;
		t.fillAlpha = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto.group_i = function () {
		var t = new eui.Group();
		this.group = t;
		t.height = 550;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 900;
		t.elementsContent = [this._Group6_i(),this.logo_i()];
		return t;
	};
	_proto._Group6_i = function () {
		var t = new eui.Group();
		t.height = 550;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.verticalCenter = 0;
		t.width = 900;
		t.x = 0;
		t.y = 0;
		t.elementsContent = [this._Rect1_i(),this._Rect2_i(),this._Rect3_i(),this._Group1_i(),this.setPan_i(),this.cancel_i(),this.sure_i(),this.question_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 100;
		t.fillColor = 0x3f4fa8;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.ellipseWidth = 100;
		t.fillColor = 0x1b2244;
		t.left = 10;
		t.right = 10;
		t.top = 10;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.ellipseWidth = 50;
		t.height = 250;
		t.horizontalCenter = 0;
		t.strokeWeight = 5;
		t.top = 150;
		t.width = 800;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.height = 50;
		t.horizontalCenter = 0;
		t.top = 80;
		t.width = 800;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this._Label1_i(),this.caoMoney_i(),this._Label2_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 15;
		t.horizontalAlign = "left";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.size = 30;
		t.text = "设置操盘金额";
		t.textColor = 0xbfc6ff;
		t.x = 57;
		t.y = 12;
		return t;
	};
	_proto.caoMoney_i = function () {
		var t = new eui.TextInput();
		this.caoMoney = t;
		t.skinName = "TextInputSkin";
		t.x = 271;
		t.y = 8;
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.size = 30;
		t.text = "注：最低操盘金额500000";
		t.textColor = 0xbfc6ff;
		t.x = 604;
		t.y = 14;
		return t;
	};
	_proto.setPan_i = function () {
		var t = new eui.Group();
		this.setPan = t;
		t.height = 200;
		t.horizontalCenter = 0;
		t.top = 180;
		t.width = 750;
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this._Group3_i(),this._Group4_i(),this._Group5_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.gap = 20;
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.x = 24;
		t.y = 6;
		t.layout = this._HorizontalLayout3_i();
		t.elementsContent = [this._RadioButton1_i(),this._Group2_i()];
		return t;
	};
	_proto._HorizontalLayout3_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 15;
		t.horizontalAlign = "left";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._RadioButton1_i = function () {
		var t = new eui.RadioButton();
		t.height = 49;
		t.horizontalCenter = 0;
		t.label = "";
		t.scaleX = 1;
		t.scaleY = 1;
		t.selected = true;
		t.verticalCenter = 0;
		t.width = 47;
		t.skinName = OperatePanelSkin$Skin5;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.x = 47;
		t.y = 10;
		t.layout = this._HorizontalLayout2_i();
		t.elementsContent = [this._Label3_i(),this.auto_i(),this._Label4_i(),this._Label5_i()];
		return t;
	};
	_proto._HorizontalLayout2_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "left";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label3_i = function () {
		var t = new eui.Label();
		t.text = "自动补足操盘余额";
		t.textColor = 0xbfc6ff;
		t.x = -3;
		t.y = -5;
		return t;
	};
	_proto.auto_i = function () {
		var t = new eui.TextInput();
		this.auto = t;
		t.height = 50;
		t.maxChars = 2;
		t.skinName = "TextInputSkin";
		t.text = "5";
		t.width = 80;
		t.x = 203;
		return t;
	};
	_proto._Label4_i = function () {
		var t = new eui.Label();
		t.text = "次";
		t.textColor = 0xbfc6ff;
		t.x = 283;
		t.y = 8;
		return t;
	};
	_proto._Label5_i = function () {
		var t = new eui.Label();
		t.size = 20;
		t.text = "(最大次数99次)";
		t.textColor = 0xBFC6FF;
		t.x = 293;
		t.y = 18;
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		t.x = 34;
		t.y = 16;
		t.layout = this._HorizontalLayout4_i();
		t.elementsContent = [this._RadioButton2_i(),this._Label6_i()];
		return t;
	};
	_proto._HorizontalLayout4_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 15;
		t.horizontalAlign = "left";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._RadioButton2_i = function () {
		var t = new eui.RadioButton();
		t.height = 49;
		t.horizontalCenter = 0;
		t.label = "";
		t.scaleX = 1;
		t.scaleY = 1;
		t.verticalCenter = 0;
		t.width = 47;
		t.skinName = OperatePanelSkin$Skin6;
		return t;
	};
	_proto._Label6_i = function () {
		var t = new eui.Label();
		t.text = "持续补足";
		t.textColor = 0xbfc6ff;
		t.x = 25;
		t.y = 22;
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		t.x = 44;
		t.y = 26;
		t.layout = this._HorizontalLayout5_i();
		t.elementsContent = [this._RadioButton3_i(),this._Label7_i(),this._Label8_i()];
		return t;
	};
	_proto._HorizontalLayout5_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 15;
		t.horizontalAlign = "left";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._RadioButton3_i = function () {
		var t = new eui.RadioButton();
		t.height = 49;
		t.horizontalCenter = 0;
		t.label = "";
		t.scaleX = 1;
		t.scaleY = 1;
		t.verticalCenter = 0;
		t.width = 47;
		t.skinName = OperatePanelSkin$Skin7;
		return t;
	};
	_proto._Label7_i = function () {
		var t = new eui.Label();
		t.text = "不补足操盘余额";
		t.textColor = 0xbfc6ff;
		t.x = 52;
		t.y = 13;
		return t;
	};
	_proto._Label8_i = function () {
		var t = new eui.Label();
		t.size = 20;
		t.text = "（当余额小于100000时自动退出操盘）";
		t.textColor = 0xbfc6ff;
		t.x = 262;
		t.y = 15;
		return t;
	};
	_proto.cancel_i = function () {
		var t = new eui.Image();
		this.cancel = t;
		t.bottom = 20;
		t.horizontalCenter = -200;
		t.source = "operate_setup_json.operate_setup4";
		return t;
	};
	_proto.sure_i = function () {
		var t = new eui.Image();
		this.sure = t;
		t.bottom = 20;
		t.horizontalCenter = 200;
		t.source = "operate_setup_json.operate_setup8";
		return t;
	};
	_proto.question_i = function () {
		var t = new eui.Image();
		this.question = t;
		t.right = 90;
		t.source = "operate_setup_json.operate_setup1";
		t.top = 180;
		return t;
	};
	_proto.logo_i = function () {
		var t = new eui.Group();
		this.logo = t;
		t.height = 100;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = -50;
		t.width = 380;
		t.x = 260;
		t.elementsContent = [this._Rect4_i(),this._Label9_i()];
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 50;
		t.fillColor = 0x151516;
		t.left = 0;
		t.right = 0;
		t.strokeAlpha = 1;
		t.strokeColor = 0x3f4fa8;
		t.strokeWeight = 10;
		t.top = 0;
		return t;
	};
	_proto._Label9_i = function () {
		var t = new eui.Label();
		t.bold = true;
		t.horizontalCenter = 0;
		t.size = 50;
		t.text = "操盘设置";
		t.textColor = 0xa0a6e5;
		t.verticalCenter = 0;
		return t;
	};
	return OperatePanelSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/ResultSkin.exml'] = window.ResultSkin = (function (_super) {
	__extends(ResultSkin, _super);
	function ResultSkin() {
		_super.call(this);
		this.skinParts = ["lingt","light0","light1","showImg","image","image0","image1","image2","stars","times"];
		
		this.height = 750;
		this.width = 1334;
		this.lingt_i();
		this.elementsContent = [this._Rect1_i(),this._Group2_i()];
		
		eui.Binding.$bindProperties(this, ["light0"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [2],[],this._Object1,"scaleX")
		eui.Binding.$bindProperties(this, [2],[],this._Object1,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object2,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object2,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object3,"alpha")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object3,"scaleX")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object3,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object4,"alpha")
		eui.Binding.$bindProperties(this, [2],[],this._Object4,"scaleX")
		eui.Binding.$bindProperties(this, [2],[],this._Object4,"scaleY")
		eui.Binding.$bindProperties(this, ["light1"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [1],[],this._Object5,"alpha")
		eui.Binding.$bindProperties(this, [1.5],[],this._Object5,"scaleX")
		eui.Binding.$bindProperties(this, [1.5],[],this._Object5,"scaleY")
		eui.Binding.$bindProperties(this, [0],[],this._Object6,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object6,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object6,"scaleY")
		eui.Binding.$bindProperties(this, ["showImg"],[0],this._TweenItem3,"target")
		eui.Binding.$bindProperties(this, [2],[],this._Object7,"scaleX")
		eui.Binding.$bindProperties(this, [2],[],this._Object7,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object8,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object8,"scaleY")
	}
	var _proto = ResultSkin.prototype;

	_proto.lingt_i = function () {
		var t = new egret.tween.TweenGroup();
		this.lingt = t;
		t.items = [this._TweenItem1_i(),this._TweenItem2_i(),this._TweenItem3_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Set1_i(),this._To1_i(),this._To2_i(),this._To3_i(),this._Wait1_i(),this._Set2_i(),this._Wait2_i(),this._Set3_i()];
		return t;
	};
	_proto._Set1_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object1_i();
		return t;
	};
	_proto._Object1_i = function () {
		var t = {};
		this._Object1 = t;
		return t;
	};
	_proto._To1_i = function () {
		var t = new egret.tween.To();
		t.duration = 500;
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto._To2_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto._To3_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object4_i();
		return t;
	};
	_proto._Object4_i = function () {
		var t = {};
		this._Object4 = t;
		return t;
	};
	_proto._Wait1_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 500;
		return t;
	};
	_proto._Set2_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._Wait2_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 1000;
		return t;
	};
	_proto._Set3_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._TweenItem2_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem2 = t;
		t.paths = [this._Set4_i(),this._To4_i(),this._Wait3_i(),this._Set5_i(),this._Wait4_i(),this._Set6_i(),this._Wait5_i(),this._Set7_i()];
		return t;
	};
	_proto._Set4_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object5_i();
		return t;
	};
	_proto._Object5_i = function () {
		var t = {};
		this._Object5 = t;
		return t;
	};
	_proto._To4_i = function () {
		var t = new egret.tween.To();
		t.duration = 500;
		t.props = this._Object6_i();
		return t;
	};
	_proto._Object6_i = function () {
		var t = {};
		this._Object6 = t;
		return t;
	};
	_proto._Wait3_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 500;
		return t;
	};
	_proto._Set5_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._Wait4_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 500;
		return t;
	};
	_proto._Set6_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._Wait5_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 1000;
		return t;
	};
	_proto._Set7_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._TweenItem3_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem3 = t;
		t.paths = [this._Set8_i(),this._To5_i(),this._Wait6_i(),this._Set9_i(),this._Wait7_i(),this._Set10_i(),this._Wait8_i(),this._Set11_i()];
		return t;
	};
	_proto._Set8_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object7_i();
		return t;
	};
	_proto._Object7_i = function () {
		var t = {};
		this._Object7 = t;
		return t;
	};
	_proto._To5_i = function () {
		var t = new egret.tween.To();
		t.duration = 250;
		t.props = this._Object8_i();
		return t;
	};
	_proto._Object8_i = function () {
		var t = {};
		this._Object8 = t;
		return t;
	};
	_proto._Wait6_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 750;
		return t;
	};
	_proto._Set9_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._Wait7_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 500;
		return t;
	};
	_proto._Set10_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._Wait8_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 1000;
		return t;
	};
	_proto._Set11_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.height = 477;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 494;
		t.elementsContent = [this._Image1_i(),this.light0_i(),this.light1_i(),this.showImg_i(),this.stars_i(),this._Group1_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "result (1)_json.result (1)1";
		t.verticalCenter = 0;
		return t;
	};
	_proto.light0_i = function () {
		var t = new eui.Image();
		this.light0 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "result (1)_json.result (1)2";
		t.verticalCenter = 0;
		return t;
	};
	_proto.light1_i = function () {
		var t = new eui.Image();
		this.light1 = t;
		t.horizontalCenter = 0;
		t.source = "result (1)_json.result (1)18";
		t.verticalCenter = 0;
		return t;
	};
	_proto.showImg_i = function () {
		var t = new eui.Image();
		this.showImg = t;
		t.horizontalCenter = 0;
		t.source = "draw_json.draw0";
		t.verticalCenter = 22.5;
		return t;
	};
	_proto.stars_i = function () {
		var t = new eui.Group();
		this.stars = t;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.elementsContent = [this.image_i(),this.image0_i(),this.image1_i(),this.image2_i(),this._Image2_i(),this._Image3_i(),this._Image4_i(),this._Image5_i(),this._Image6_i(),this._Image7_i(),this._Image8_i(),this._Image9_i(),this._Image10_i(),this._Image11_i(),this._Image12_i(),this._Image13_i(),this._Image14_i(),this._Image15_i(),this._Image16_i(),this._Image17_i(),this._Image18_i()];
		return t;
	};
	_proto.image_i = function () {
		var t = new eui.Image();
		this.image = t;
		t.horizontalCenter = 0;
		t.source = "result (1)_json.result (1)10";
		t.top = 35;
		return t;
	};
	_proto.image0_i = function () {
		var t = new eui.Image();
		this.image0 = t;
		t.horizontalCenter = 22;
		t.source = "result (1)_json.result (1)11";
		t.top = 68;
		return t;
	};
	_proto.image1_i = function () {
		var t = new eui.Image();
		this.image1 = t;
		t.horizontalCenter = 38;
		t.source = "result (1)_json.result (1)11";
		t.top = 106;
		return t;
	};
	_proto.image2_i = function () {
		var t = new eui.Image();
		this.image2 = t;
		t.horizontalCenter = 56;
		t.source = "result (1)_json.result (1)11";
		t.top = 144;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 96;
		t.source = "result (1)_json.result (1)11";
		t.top = 159;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 154;
		t.source = "result (1)_json.result (1)11";
		t.top = 166;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 192;
		t.source = "result (1)_json.result (1)11";
		t.top = 176;
		return t;
	};
	_proto._Image5_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 162;
		t.source = "result (1)_json.result (1)11";
		t.top = 208;
		return t;
	};
	_proto._Image6_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 128;
		t.source = "result (1)_json.result (1)11";
		t.top = 240;
		return t;
	};
	_proto._Image7_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 96;
		t.source = "result (1)_json.result (1)11";
		t.top = 278;
		return t;
	};
	_proto._Image8_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 109;
		t.source = "result (1)_json.result (1)11";
		t.top = 329;
		return t;
	};
	_proto._Image9_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -23;
		t.source = "result (1)_json.result (1)12";
		t.top = 68;
		return t;
	};
	_proto._Image10_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -42;
		t.source = "result (1)_json.result (1)12";
		t.top = 111;
		return t;
	};
	_proto._Image11_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -61;
		t.source = "result (1)_json.result (1)12";
		t.top = 150;
		return t;
	};
	_proto._Image12_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -101;
		t.source = "result (1)_json.result (1)12";
		t.top = 159;
		return t;
	};
	_proto._Image13_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -152;
		t.source = "result (1)_json.result (1)12";
		t.top = 166;
		return t;
	};
	_proto._Image14_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -192;
		t.source = "result (1)_json.result (1)12";
		t.top = 174;
		return t;
	};
	_proto._Image15_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -162;
		t.source = "result (1)_json.result (1)12";
		t.top = 209;
		return t;
	};
	_proto._Image16_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -130;
		t.source = "result (1)_json.result (1)12";
		t.top = 239;
		return t;
	};
	_proto._Image17_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -101;
		t.source = "result (1)_json.result (1)12";
		t.top = 278;
		return t;
	};
	_proto._Image18_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -111;
		t.source = "result (1)_json.result (1)12";
		t.top = 331;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.height = 106;
		t.horizontalCenter = 0;
		t.width = 401;
		t.elementsContent = [this._Image19_i(),this.times_i()];
		return t;
	};
	_proto._Image19_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "result (1)_json.result (1)52";
		t.x = -25;
		t.y = 94;
		return t;
	};
	_proto.times_i = function () {
		var t = new eui.Label();
		this.times = t;
		t.bottom = 0;
		t.left = 15;
		t.right = 10;
		t.size = 80;
		t.stroke = 3;
		t.text = "22.71倍";
		t.textAlign = "center";
		t.textColor = 0xedbf23;
		t.top = 10;
		t.verticalAlign = "middle";
		return t;
	};
	return ResultSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/SetUpSkin.exml'] = window.SetUpSkin = (function (_super) {
	__extends(SetUpSkin, _super);
	var SetUpSkin$Skin8 = 	(function (_super) {
		__extends(SetUpSkin$Skin8, _super);
		function SetUpSkin$Skin8() {
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
						new eui.SetProperty("_Image1","source","setup02_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = SetUpSkin$Skin8.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "setup01_png";
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
		return SetUpSkin$Skin8;
	})(eui.Skin);

	function SetUpSkin() {
		_super.call(this);
		this.skinParts = ["move","back","music","help","history","home_page","group"];
		
		this.height = 750;
		this.width = 1334;
		this.move_i();
		this.elementsContent = [this.back_i(),this.group_i()];
		
		eui.Binding.$bindProperties(this, ["group"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object1,"scaleX")
		eui.Binding.$bindProperties(this, [0],[],this._Object1,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object2,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object2,"scaleY")
	}
	var _proto = SetUpSkin.prototype;

	_proto.move_i = function () {
		var t = new egret.tween.TweenGroup();
		this.move = t;
		t.items = [this._TweenItem1_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Set1_i(),this._To1_i()];
		return t;
	};
	_proto._Set1_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object1_i();
		return t;
	};
	_proto._Object1_i = function () {
		var t = {};
		this._Object1 = t;
		return t;
	};
	_proto._To1_i = function () {
		var t = new egret.tween.To();
		t.duration = 300;
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto.back_i = function () {
		var t = new eui.Rect();
		this.back = t;
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto.group_i = function () {
		var t = new eui.Group();
		this.group = t;
		t.right = 20;
		t.top = 100;
		t.width = 270;
		t.elementsContent = [this._Image1_i(),this._Group2_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "setup_json.setup7";
		t.top = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.width = 270;
		t.x = 0;
		t.y = 10;
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this._Group1_i(),this.help_i(),this.history_i(),this.home_page_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.gap = 15;
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.height = 80;
		t.horizontalCenter = 0;
		t.width = 270;
		t.y = 24;
		t.elementsContent = [this._Image2_i(),this._Label1_i(),this.music_i(),this._Rect1_i()];
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.left = 20;
		t.source = "setup_json.setup5";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = -25;
		t.size = 30;
		t.text = "音乐";
		t.verticalCenter = 0;
		return t;
	};
	_proto.music_i = function () {
		var t = new eui.ToggleSwitch();
		this.music = t;
		t.height = 40;
		t.label = "";
		t.right = 20;
		t.selected = true;
		t.verticalCenter = 0;
		t.width = 104;
		t.skinName = SetUpSkin$Skin8;
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillColor = 0xffffff;
		t.height = 2;
		t.horizontalCenter = 0;
		t.percentWidth = 80;
		return t;
	};
	_proto.help_i = function () {
		var t = new eui.Group();
		this.help = t;
		t.height = 80;
		t.horizontalCenter = 0;
		t.width = 270;
		t.y = 24;
		t.elementsContent = [this._Image3_i(),this._Label2_i(),this._Image4_i(),this._Rect2_i()];
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.left = 20;
		t.source = "setup_json.setup4";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = -25;
		t.size = 30;
		t.text = "帮助";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.right = 20;
		t.source = "setup_json.setup6";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillColor = 0xFFFFFF;
		t.height = 2;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.percentWidth = 80;
		t.x = 27;
		t.y = 95;
		return t;
	};
	_proto.history_i = function () {
		var t = new eui.Group();
		this.history = t;
		t.height = 80;
		t.horizontalCenter = 0;
		t.width = 270;
		t.x = 10;
		t.y = 34;
		t.elementsContent = [this._Image5_i(),this._Label3_i(),this._Image6_i(),this._Rect3_i()];
		return t;
	};
	_proto._Image5_i = function () {
		var t = new eui.Image();
		t.left = 20;
		t.source = "setup_json.setup4";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Label3_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = 0;
		t.size = 30;
		t.text = "历史记录";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image6_i = function () {
		var t = new eui.Image();
		t.right = 20;
		t.source = "setup_json.setup6";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillColor = 0xFFFFFF;
		t.height = 2;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.percentWidth = 80;
		t.x = 27;
		t.y = 95;
		return t;
	};
	_proto.home_page_i = function () {
		var t = new eui.Group();
		this.home_page = t;
		t.height = 80;
		t.horizontalCenter = 0;
		t.width = 270;
		t.y = 24;
		t.elementsContent = [this._Image7_i(),this._Label4_i(),this._Image8_i()];
		return t;
	};
	_proto._Image7_i = function () {
		var t = new eui.Image();
		t.left = 20;
		t.source = "header_json.header7";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Label4_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = -25;
		t.size = 30;
		t.text = "首页";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image8_i = function () {
		var t = new eui.Image();
		t.right = 20;
		t.source = "setup_json.setup6";
		t.verticalCenter = 0;
		return t;
	};
	return SetUpSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/payPanel/DiamondsItemSkin.exml'] = window.DiamondsItemSkin = (function (_super) {
	__extends(DiamondsItemSkin, _super);
	function DiamondsItemSkin() {
		_super.call(this);
		this.skinParts = ["money","diamonds","award"];
		
		this.height = 104;
		this.width = 661;
		this.elementsContent = [this._Group6_i()];
	}
	var _proto = DiamondsItemSkin.prototype;

	_proto._Group6_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.elementsContent = [this._Rect1_i(),this._Group2_i(),this._Group3_i(),this._Group5_i(),this._Image1_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillColor = 0x262932;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.anchorOffsetY = 0;
		t.right = 22;
		t.verticalCenter = 0;
		t.elementsContent = [this._Rect2_i(),this._Group1_i()];
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 20;
		t.fillColor = 0xde433f;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this.money_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.paddingBottom = 20;
		t.paddingLeft = 15;
		t.paddingRight = 15;
		t.paddingTop = 20;
		return t;
	};
	_proto.money_i = function () {
		var t = new eui.Label();
		this.money = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 26;
		t.text = "￥100 购买";
		t.verticalCenter = 0;
		t.x = 26;
		t.y = 35;
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 37;
		t.verticalCenter = -20.5;
		t.width = 288;
		t.x = 92;
		t.layout = this._HorizontalLayout2_i();
		t.elementsContent = [this.diamonds_i(),this._Label1_i()];
		return t;
	};
	_proto._HorizontalLayout2_i = function () {
		var t = new eui.HorizontalLayout();
		t.verticalAlign = "middle";
		return t;
	};
	_proto.diamonds_i = function () {
		var t = new eui.Label();
		this.diamonds = t;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 30;
		t.text = "100";
		t.textColor = 0xf2a204;
		t.x = 10;
		t.y = 3;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 28;
		t.text = "钻石";
		t.textColor = 0xffffff;
		t.x = 20;
		t.y = 13;
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.x = 92;
		t.y = 54;
		t.elementsContent = [this._Rect3_i(),this._Group4_i()];
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 20;
		t.fillColor = 0xE08100;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.layout = this._HorizontalLayout3_i();
		t.elementsContent = [this.award_i()];
		return t;
	};
	_proto._HorizontalLayout3_i = function () {
		var t = new eui.HorizontalLayout();
		t.paddingBottom = 8;
		t.paddingLeft = 8;
		t.paddingRight = 8;
		t.paddingTop = 8;
		return t;
	};
	_proto.award_i = function () {
		var t = new eui.Label();
		this.award = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 22;
		t.text = "额外赠送";
		t.verticalCenter = 0;
		t.x = 31;
		t.y = 9;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "zuanshi_icon_png";
		t.verticalCenter = 0;
		t.x = 20;
		t.y = 27;
		return t;
	};
	return DiamondsItemSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/payPanel/PayCheckBoxSkin.exml'] = window.skins.PayCheckBoxSkin = (function (_super) {
	__extends(PayCheckBoxSkin, _super);
	function PayCheckBoxSkin() {
		_super.call(this);
		this.skinParts = ["labelDisplay"];
		
		this.elementsContent = [this._Group1_i()];
		this.states = [
			new eui.State ("up",
				[
					new eui.SetProperty("_Image1","source","checkbox_unselect2_png")
				])
			,
			new eui.State ("upAndSelected",
				[
					new eui.SetProperty("_Image1","source","checkbox_select_up2_png")
				])
			,
			new eui.State ("downAndSelected",
				[
					new eui.SetProperty("_Image1","source","checkbox_select_up2_png")
				])
		];
	}
	var _proto = PayCheckBoxSkin.prototype;

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
		t.source = "checkbox_unselect2_png";
		return t;
	};
	_proto.labelDisplay_i = function () {
		var t = new eui.Label();
		this.labelDisplay = t;
		t.fontFamily = "Tahoma";
		t.size = 22;
		t.textAlign = "center";
		t.textColor = 0x999999;
		t.verticalAlign = "middle";
		return t;
	};
	return PayCheckBoxSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/payPanel/PayItemSkin.exml'] = window.PayItemSkin = (function (_super) {
	__extends(PayItemSkin, _super);
	function PayItemSkin() {
		_super.call(this);
		this.skinParts = ["money","coin","award"];
		
		this.height = 104;
		this.width = 661;
		this.elementsContent = [this._Group6_i()];
	}
	var _proto = PayItemSkin.prototype;

	_proto._Group6_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.elementsContent = [this._Rect1_i(),this._Group2_i(),this._Group3_i(),this._Group5_i(),this._Image1_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillColor = 0x262932;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.anchorOffsetY = 0;
		t.right = 22;
		t.verticalCenter = 0;
		t.elementsContent = [this._Rect2_i(),this._Group1_i()];
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 20;
		t.fillColor = 0xde433f;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this.money_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.paddingBottom = 20;
		t.paddingLeft = 15;
		t.paddingRight = 15;
		t.paddingTop = 20;
		return t;
	};
	_proto.money_i = function () {
		var t = new eui.Label();
		this.money = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 26;
		t.text = "￥100 购买";
		t.verticalCenter = 0;
		t.x = 26;
		t.y = 35;
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 37;
		t.width = 288;
		t.x = 92;
		t.y = 13;
		t.layout = this._HorizontalLayout2_i();
		t.elementsContent = [this.coin_i()];
		return t;
	};
	_proto._HorizontalLayout2_i = function () {
		var t = new eui.HorizontalLayout();
		t.verticalAlign = "middle";
		return t;
	};
	_proto.coin_i = function () {
		var t = new eui.Label();
		this.coin = t;
		t.scaleX = 1;
		t.scaleY = 1;
		t.text = "100";
		t.x = 10;
		t.y = 13;
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.x = 92;
		t.y = 54;
		t.elementsContent = [this._Rect3_i(),this._Group4_i()];
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 20;
		t.fillColor = 0xe08100;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.layout = this._HorizontalLayout3_i();
		t.elementsContent = [this.award_i()];
		return t;
	};
	_proto._HorizontalLayout3_i = function () {
		var t = new eui.HorizontalLayout();
		t.paddingBottom = 8;
		t.paddingLeft = 8;
		t.paddingRight = 8;
		t.paddingTop = 8;
		return t;
	};
	_proto.award_i = function () {
		var t = new eui.Label();
		this.award = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 22;
		t.text = "额外赠送";
		t.verticalCenter = 0;
		t.x = 31;
		t.y = 9;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "common_jd_png";
		t.verticalCenter = 0;
		t.x = 17.88;
		t.y = 27;
		return t;
	};
	return PayItemSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/payPanel/PayPanelSkin.exml'] = window.PayPanelSkin = (function (_super) {
	__extends(PayPanelSkin, _super);
	function PayPanelSkin() {
		_super.call(this);
		this.skinParts = ["close_bg","tips0","loading_group","scroller_box","scroller","coin","diamonds","title","auto_zh","pay_group"];
		
		this.height = 700;
		this.elementsContent = [this.close_bg_i(),this._Group6_i()];
	}
	var _proto = PayPanelSkin.prototype;

	_proto.close_bg_i = function () {
		var t = new eui.Rect();
		this.close_bg = t;
		t.bottom = 0;
		t.fillAlpha = 0.4;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group6_i = function () {
		var t = new eui.Group();
		t.anchorOffsetY = 0;
		t.bottom = 120;
		t.horizontalCenter = 0;
		t.scaleX = 0.85;
		t.scaleY = 0.85;
		t.top = 120;
		t.width = 661;
		t.elementsContent = [this._Group5_i(),this._Image3_i(),this.title_i(),this.pay_group_i()];
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		t.anchorOffsetY = 0;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 56;
		t.x = 0;
		t.y = 56;
		t.elementsContent = [this._Rect1_i(),this.loading_group_i(),this.scroller_i(),this._Group3_i(),this._Group4_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 40;
		t.fillColor = 0x262932;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto.loading_group_i = function () {
		var t = new eui.Group();
		this.loading_group = t;
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 62;
		t.horizontalCenter = 0.5;
		t.y = 285.08;
		t.elementsContent = [this._Rect2_i(),this.tips0_i()];
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.ellipseWidth = 23;
		t.fillAlpha = 0.2;
		t.fillColor = 0xFFFFFF;
		t.height = 50;
		t.verticalCenter = 0;
		t.width = 528;
		t.x = 0;
		return t;
	};
	_proto.tips0_i = function () {
		var t = new eui.Label();
		this.tips0 = t;
		t.size = 18;
		t.text = "数据加载中...";
		t.verticalCenter = 0;
		t.x = 201;
		return t;
	};
	_proto.scroller_i = function () {
		var t = new eui.Scroller();
		this.scroller = t;
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.bottom = 38.66999999999996;
		t.horizontalCenter = 0.5;
		t.scrollPolicyH = "off";
		t.top = 269;
		t.width = 619.66;
		t.viewport = this.scroller_box_i();
		return t;
	};
	_proto.scroller_box_i = function () {
		var t = new eui.Group();
		this.scroller_box = t;
		t.anchorOffsetY = 0;
		t.layout = this._VerticalLayout1_i();
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.horizontalAlign = "justify";
		t.verticalAlign = "top";
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.anchorOffsetY = 0;
		t.height = 82;
		t.left = 0;
		t.right = 0;
		t.y = 52.7;
		t.elementsContent = [this._Group1_i(),this._Group2_i()];
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 59;
		t.width = 236;
		t.x = 58;
		t.y = 10;
		t.elementsContent = [this._Rect3_i(),this._Image1_i(),this.coin_i()];
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.ellipseWidth = 33;
		t.fillColor = 0x181e2d;
		t.height = 33;
		t.left = 43;
		t.right = 8;
		t.strokeColor = 0x646e84;
		t.strokeWeight = 2;
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 41.75;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "common_jd_png";
		t.verticalCenter = 0.5;
		t.width = 47.59;
		t.x = 13.76;
		return t;
	};
	_proto.coin_i = function () {
		var t = new eui.Label();
		this.coin = t;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 26;
		t.text = "- -";
		t.verticalCenter = 0.5;
		t.x = 68.5;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 59;
		t.width = 236;
		t.x = 364;
		t.y = 10;
		t.elementsContent = [this._Rect4_i(),this._Image2_i(),this.diamonds_i()];
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.ellipseWidth = 33;
		t.fillColor = 0x181E2D;
		t.height = 33;
		t.left = 43;
		t.right = 8;
		t.strokeColor = 0x646E84;
		t.strokeWeight = 2;
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "zuanshi_icon_png";
		t.verticalCenter = 0.5;
		t.x = 17.3;
		return t;
	};
	_proto.diamonds_i = function () {
		var t = new eui.Label();
		this.diamonds = t;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 26;
		t.text = "- -";
		t.verticalCenter = 0.5;
		t.x = 68.5;
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		t.anchorOffsetY = 0;
		t.left = 0;
		t.right = 0;
		t.y = 136.17;
		t.elementsContent = [this._Rect5_i(),this._Label1_i()];
		return t;
	};
	_proto._Rect5_i = function () {
		var t = new eui.Rect();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.ellipseWidth = 40;
		t.fillColor = 0x1f2025;
		t.height = 50;
		t.horizontalCenter = 0;
		t.top = 0;
		t.width = 580;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = 0;
		t.size = 26;
		t.text = " 适度娱乐，理性消费！";
		t.textColor = 0x727890;
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "modal_bg_top_png";
		t.x = 0;
		t.y = 0;
		return t;
	};
	_proto.title_i = function () {
		var t = new eui.Label();
		this.title = t;
		t.horizontalCenter = 0.5;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 34;
		t.text = "充值金币";
		t.x = 263;
		t.y = 52.34;
		return t;
	};
	_proto.pay_group_i = function () {
		var t = new eui.Group();
		this.pay_group = t;
		t.height = 54;
		t.horizontalCenter = 0.5;
		t.width = 580;
		t.y = 255.1;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this.auto_zh_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 0;
		t.horizontalAlign = "left";
		t.paddingLeft = 0;
		t.paddingRight = 0;
		t.verticalAlign = "middle";
		return t;
	};
	_proto.auto_zh_i = function () {
		var t = new eui.CheckBox();
		this.auto_zh = t;
		t.label = "购买钻石转换金币";
		t.selected = true;
		t.skinName = "skins.PayCheckBoxSkin";
		t.x = 53;
		t.y = 15.5;
		return t;
	};
	return PayPanelSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/payPanel/TagPayBotton.exml'] = window.TagPayBotton = (function (_super) {
	__extends(TagPayBotton, _super);
	function TagPayBotton() {
		_super.call(this);
		this.skinParts = ["labelDisplay"];
		
		this.currentState = "up";
		this.height = 54;
		this.width = 200;
		this.elementsContent = [this._Rect1_i(),this.labelDisplay_i()];
		this.states = [
			new eui.State ("down",
				[
					new eui.SetProperty("_Rect1","fillColor",0xd54535),
					new eui.SetProperty("_Rect1","left",0),
					new eui.SetProperty("_Rect1","right",0),
					new eui.SetProperty("_Rect1","top",0),
					new eui.SetProperty("_Rect1","bottom",0),
					new eui.SetProperty("_Rect1","ellipseWidth",0)
				])
			,
			new eui.State ("up",
				[
					new eui.SetProperty("_Rect1","left",0),
					new eui.SetProperty("_Rect1","right",0),
					new eui.SetProperty("_Rect1","top",0),
					new eui.SetProperty("_Rect1","bottom",0),
					new eui.SetProperty("_Rect1","ellipseWidth",0)
				])
		];
	}
	var _proto = TagPayBotton.prototype;

	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		this._Rect1 = t;
		t.anchorOffsetX = 0;
		t.bottom = 0;
		t.ellipseWidth = 54;
		t.fillColor = 0x1f2025;
		t.left = 0;
		t.right = -39;
		t.top = 0;
		return t;
	};
	_proto.labelDisplay_i = function () {
		var t = new eui.Label();
		this.labelDisplay = t;
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.size = 26;
		t.text = "Label";
		t.textAlign = "center";
		t.top = 0;
		t.verticalAlign = "middle";
		return t;
	};
	return TagPayBotton;
})(eui.Skin);