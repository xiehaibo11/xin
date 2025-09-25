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
})(eui.Skin);generateEUI.paths['resource/eui_skins/LoadingUISkin.exml'] = window.LoadingUISkin = (function (_super) {
	__extends(LoadingUISkin, _super);
	function LoadingUISkin() {
		_super.call(this);
		this.skinParts = ["load","pro_bg","rect_mask","image","image0","image1","loading"];
		
		this.height = 1136;
		this.width = 640;
		this.load_i();
		this.elementsContent = [this._Rect1_i(),this._Group1_i(),this._Group2_i(),this.loading_i()];
		
		eui.Binding.$bindProperties(this, ["image"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object1,"alpha")
		eui.Binding.$bindProperties(this, ["image0"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object2,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object3,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object4,"alpha")
		eui.Binding.$bindProperties(this, ["image1"],[0],this._TweenItem3,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object5,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object6,"alpha")
	}
	var _proto = LoadingUISkin.prototype;

	_proto.load_i = function () {
		var t = new egret.tween.TweenGroup();
		this.load = t;
		t.items = [this._TweenItem1_i(),this._TweenItem2_i(),this._TweenItem3_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Wait1_i(),this._Set1_i(),this._Wait2_i(),this._Set2_i()];
		return t;
	};
	_proto._Wait1_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 250;
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
	_proto._Wait2_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 500;
		return t;
	};
	_proto._Set2_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._TweenItem2_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem2 = t;
		t.paths = [this._Set3_i(),this._Wait3_i(),this._Set4_i(),this._Wait4_i(),this._Set5_i(),this._Wait5_i(),this._Set6_i()];
		return t;
	};
	_proto._Set3_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto._Wait3_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 250;
		return t;
	};
	_proto._Set4_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto._Wait4_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 250;
		return t;
	};
	_proto._Set5_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object4_i();
		return t;
	};
	_proto._Object4_i = function () {
		var t = {};
		this._Object4 = t;
		return t;
	};
	_proto._Wait5_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 250;
		return t;
	};
	_proto._Set6_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._TweenItem3_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem3 = t;
		t.paths = [this._Set7_i(),this._Wait6_i(),this._Set8_i(),this._Wait7_i(),this._Set9_i()];
		return t;
	};
	_proto._Set7_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object5_i();
		return t;
	};
	_proto._Object5_i = function () {
		var t = {};
		this._Object5 = t;
		return t;
	};
	_proto._Wait6_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 500;
		return t;
	};
	_proto._Set8_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object6_i();
		return t;
	};
	_proto._Object6_i = function () {
		var t = {};
		this._Object6 = t;
		return t;
	};
	_proto._Wait7_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 250;
		return t;
	};
	_proto._Set9_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillColor = 0x684018;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.elementsContent = [this._Image1_i(),this.pro_bg_i(),this.rect_mask_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-loading003_png";
		return t;
	};
	_proto.pro_bg_i = function () {
		var t = new eui.Image();
		this.pro_bg = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "loadingMask_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.rect_mask_i = function () {
		var t = new eui.Rect();
		this.rect_mask = t;
		t.anchorOffsetY = 0;
		t.bottom = -1;
		t.fillAlpha = 1;
		t.fillColor = 0xdb9813;
		t.height = 0;
		t.left = 0;
		t.right = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.height = 200;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 200;
		t.elementsContent = [this.image_i(),this.image0_i(),this.image1_i()];
		return t;
	};
	_proto.image_i = function () {
		var t = new eui.Image();
		this.image = t;
		t.horizontalCenter = 0;
		t.source = "sprite-loading000_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.image0_i = function () {
		var t = new eui.Image();
		this.image0 = t;
		t.alpha = 0;
		t.horizontalCenter = 0;
		t.source = "sprite-loading001_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.image1_i = function () {
		var t = new eui.Image();
		this.image1 = t;
		t.alpha = 0;
		t.horizontalCenter = 0;
		t.source = "sprite-loading002_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.loading_i = function () {
		var t = new eui.Label();
		this.loading = t;
		t.horizontalCenter = 0;
		t.text = "正在加载中。。。0%";
		t.verticalCenter = 150;
		return t;
	};
	return LoadingUISkin;
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
		t.fillColor = 0xc98536;
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
		t.size = 30;
		t.textAlign = "center";
		t.textColor = 0x000000;
		t.verticalAlign = "middle";
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
		t.size = 30;
		t.textAlign = "center";
		t.textColor = 0xffffff;
		t.touchEnabled = false;
		t.verticalAlign = "middle";
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
						new eui.SetProperty("_Image1","source","btn2_png")
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
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "btn1_png";
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

	function AlertSkin() {
		_super.call(this);
		this.skinParts = ["alert","close"];
		
		this.height = 1134;
		this.width = 640;
		this.elementsContent = [this._Rect1_i(),this._Group1_i()];
	}
	var _proto = AlertSkin.prototype;

	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.height = 400;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 400;
		t.elementsContent = [this._Image1_i(),this._Label1_i(),this.alert_i(),this.close_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "alert2_png";
		t.top = 0;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = 0;
		t.text = "温馨提示";
		t.textColor = 0xdb5555;
		t.top = 25;
		return t;
	};
	_proto.alert_i = function () {
		var t = new eui.Label();
		this.alert = t;
		t.anchorOffsetX = 0;
		t.horizontalCenter = 1.5;
		t.text = "你正在创建玩家对战房间，是否继续？";
		t.textAlign = "left";
		t.textColor = 0xce7939;
		t.top = 137;
		t.verticalAlign = "middle";
		t.width = 307;
		return t;
	};
	_proto.close_i = function () {
		var t = new eui.Button();
		this.close = t;
		t.bottom = 60;
		t.height = 70;
		t.horizontalCenter = 0;
		t.label = "确定";
		t.width = 120;
		t.skinName = AlertSkin$Skin1;
		return t;
	};
	return AlertSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/InputSkin.exml'] = window.InputSkin = (function (_super) {
	__extends(InputSkin, _super);
	function InputSkin() {
		_super.call(this);
		this.skinParts = ["textDisplay","promptDisplay"];
		
		this.height = 50;
		this.width = 300;
		this.elementsContent = [this._Rect1_i(),this.textDisplay_i()];
		this.promptDisplay_i();
		
		this.states = [
			new eui.State ("normal",
				[
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
	var _proto = InputSkin.prototype;

	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.fillColor = 0xf2debc;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto.textDisplay_i = function () {
		var t = new eui.EditableText();
		this.textDisplay = t;
		t.height = 50;
		t.left = "10";
		t.right = "10";
		t.text = "";
		t.verticalAlign = "middle";
		t.verticalCenter = "0";
		return t;
	};
	_proto.promptDisplay_i = function () {
		var t = new eui.Label();
		this.promptDisplay = t;
		t.border = true;
		t.borderColor = 0x896c6c;
		t.bottom = 0;
		t.left = 10;
		t.right = 10;
		t.text = "";
		t.top = 0;
		return t;
	};
	return InputSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/AutoGameSkin.exml'] = window.AutoGameSkin = (function (_super) {
	__extends(AutoGameSkin, _super);
	var AutoGameSkin$Skin2 = 	(function (_super) {
		__extends(AutoGameSkin$Skin2, _super);
		function AutoGameSkin$Skin2() {
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
						new eui.SetProperty("_Image1","source","read_fight2_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = AutoGameSkin$Skin2.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "read_fight_png";
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
		return AutoGameSkin$Skin2;
	})(eui.Skin);

	function AutoGameSkin() {
		_super.call(this);
		this.skinParts = ["back1","sc","back","plunder","Beplunder","Single","auto_selected","autoSoldier","auto_go"];
		
		this.height = 1136;
		this.width = 640;
		this.elementsContent = [this._Group2_i()];
	}
	var _proto = AutoGameSkin.prototype;

	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.elementsContent = [this.back1_i(),this.sc_i(),this._Image1_i(),this.back_i(),this._Label1_i(),this._Group1_i()];
		return t;
	};
	_proto.back1_i = function () {
		var t = new eui.Rect();
		this.back1 = t;
		t.bottom = 0;
		t.fillAlpha = 0;
		t.left = 0;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 0;
		t.x = -120;
		t.y = -228;
		return t;
	};
	_proto.sc_i = function () {
		var t = new eui.Group();
		this.sc = t;
		t.bottom = 150;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 250;
		t.width = 600;
		t.elementsContent = [this._Rect1_i(),this._Rect2_i(),this._Rect3_i(),this._Rect4_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 10;
		t.fillColor = 0xb4712f;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 3;
		t.ellipseWidth = 10;
		t.fillColor = 0xf1d193;
		t.left = 3;
		t.right = 3;
		t.top = 3;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.ellipseWidth = 30;
		t.fillColor = 0xf2debc;
		t.left = 10;
		t.right = 10;
		t.top = 10;
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.fillColor = 0xe2b891;
		t.left = 10;
		t.right = 10;
		t.top = 60;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-dialog_json.sprite-dialog001";
		t.top = 150;
		return t;
	};
	_proto.back_i = function () {
		var t = new eui.Group();
		this.back = t;
		t.height = 40;
		t.right = 70;
		t.top = 210;
		t.width = 40;
		t.elementsContent = [this._Rect5_i(),this._Image2_i()];
		return t;
	};
	_proto._Rect5_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 40;
		t.fillColor = 0xf78d02;
		t.left = 0;
		t.right = 0;
		t.strokeColor = 0x845625;
		t.strokeWeight = 3;
		t.top = 0;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "sprite-dialog_json.sprite-dialog000";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = 0;
		t.text = "自动战斗";
		t.top = 210;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.bottom = 285;
		t.horizontalCenter = 0;
		t.top = 360;
		t.width = 580;
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this.plunder_i(),this.Beplunder_i(),this.Single_i(),this.autoSoldier_i(),this.auto_go_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto.plunder_i = function () {
		var t = new eui.Group();
		this.plunder = t;
		t.height = 80;
		t.percentWidth = 100;
		t.x = 98;
		t.y = 35;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this._Label2_i(),this._TextInput1_i(),this._Image3_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "left";
		t.paddingLeft = 30;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.text = "掠夺金豆：";
		t.textColor = 0xce7939;
		t.x = 113;
		t.y = 17;
		return t;
	};
	_proto._TextInput1_i = function () {
		var t = new eui.TextInput();
		t.skinName = "InputSkin";
		t.text = "输入数量";
		t.x = 261;
		t.y = 38;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.source = "question_png";
		t.x = 413;
		t.y = 14;
		return t;
	};
	_proto.Beplunder_i = function () {
		var t = new eui.Group();
		this.Beplunder = t;
		t.height = 80;
		t.percentWidth = 100;
		t.x = 108;
		t.y = 45;
		t.layout = this._HorizontalLayout2_i();
		t.elementsContent = [this._Label3_i(),this._TextInput2_i(),this._Image4_i()];
		return t;
	};
	_proto._HorizontalLayout2_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "left";
		t.paddingLeft = 30;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label3_i = function () {
		var t = new eui.Label();
		t.size = 25;
		t.text = "被掠夺金豆：";
		t.textColor = 0xCE7939;
		t.x = 113;
		t.y = 17;
		return t;
	};
	_proto._TextInput2_i = function () {
		var t = new eui.TextInput();
		t.skinName = "InputSkin";
		t.text = "输入数量";
		t.x = 261;
		t.y = 38;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.source = "question_png";
		t.x = 413;
		t.y = 14;
		return t;
	};
	_proto.Single_i = function () {
		var t = new eui.Group();
		this.Single = t;
		t.height = 80;
		t.percentWidth = 100;
		t.x = 118;
		t.y = 55;
		t.layout = this._HorizontalLayout3_i();
		t.elementsContent = [this._Label4_i(),this._TextInput3_i(),this._Image5_i()];
		return t;
	};
	_proto._HorizontalLayout3_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "left";
		t.paddingLeft = 30;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label4_i = function () {
		var t = new eui.Label();
		t.text = "单局出兵：";
		t.textColor = 0xCE7939;
		t.x = 113;
		t.y = 17;
		return t;
	};
	_proto._TextInput3_i = function () {
		var t = new eui.TextInput();
		t.skinName = "InputSkin";
		t.text = "输入数量";
		t.x = 261;
		t.y = 38;
		return t;
	};
	_proto._Image5_i = function () {
		var t = new eui.Image();
		t.source = "question_png";
		t.x = 413;
		t.y = 14;
		return t;
	};
	_proto.autoSoldier_i = function () {
		var t = new eui.Group();
		this.autoSoldier = t;
		t.height = 80;
		t.percentWidth = 100;
		t.x = 128;
		t.y = 65;
		t.layout = this._HorizontalLayout5_i();
		t.elementsContent = [this._Label5_i(),this.auto_selected_i(),this._Image6_i()];
		return t;
	};
	_proto._HorizontalLayout5_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "left";
		t.paddingLeft = 30;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label5_i = function () {
		var t = new eui.Label();
		t.text = "自动出兵：";
		t.textColor = 0xCE7939;
		t.x = 113;
		t.y = 17;
		return t;
	};
	_proto.auto_selected_i = function () {
		var t = new eui.Group();
		this.auto_selected = t;
		t.width = 300;
		t.x = 209;
		t.y = 10;
		t.layout = this._HorizontalLayout4_i();
		t.elementsContent = [this._RadioButton1_i(),this._RadioButton2_i(),this._RadioButton3_i(),this._RadioButton4_i()];
		return t;
	};
	_proto._HorizontalLayout4_i = function () {
		var t = new eui.HorizontalLayout();
		return t;
	};
	_proto._RadioButton1_i = function () {
		var t = new eui.RadioButton();
		t.label = "随机";
		t.scaleX = 1;
		t.scaleY = 1;
		t.selected = true;
		t.x = -74;
		t.y = -6;
		return t;
	};
	_proto._RadioButton2_i = function () {
		var t = new eui.RadioButton();
		t.label = "矛兵";
		t.scaleX = 1;
		t.scaleY = 1;
		t.x = -222;
		t.y = -11;
		return t;
	};
	_proto._RadioButton3_i = function () {
		var t = new eui.RadioButton();
		t.label = "盾兵";
		t.scaleX = 1;
		t.scaleY = 1;
		t.x = -148;
		t.y = -11;
		return t;
	};
	_proto._RadioButton4_i = function () {
		var t = new eui.RadioButton();
		t.label = "弓兵";
		t.scaleX = 1;
		t.scaleY = 1;
		t.x = -74;
		t.y = -11;
		return t;
	};
	_proto._Image6_i = function () {
		var t = new eui.Image();
		t.source = "question_png";
		t.x = 413;
		t.y = 14;
		return t;
	};
	_proto.auto_go_i = function () {
		var t = new eui.Button();
		this.auto_go = t;
		t.label = "";
		t.x = 238;
		t.y = 436;
		t.skinName = AutoGameSkin$Skin2;
		return t;
	};
	return AutoGameSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/AutoPanelSkin.exml'] = window.AutoPanelSkin = (function (_super) {
	__extends(AutoPanelSkin, _super);
	var AutoPanelSkin$Skin3 = 	(function (_super) {
		__extends(AutoPanelSkin$Skin3, _super);
		function AutoPanelSkin$Skin3() {
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
		var _proto = AutoPanelSkin$Skin3.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			t.percentHeight = 100;
			t.source = "auto_fight-1_png";
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
		return AutoPanelSkin$Skin3;
	})(eui.Skin);

	function AutoPanelSkin() {
		_super.call(this);
		this.skinParts = ["sc","winMoney","loseMoney","NowWinMoney","NowLoseMoney","cancel"];
		
		this.height = 1136;
		this.width = 640;
		this.elementsContent = [this._Rect1_i(),this.sc_i(),this._Image1_i(),this._Label1_i(),this._Group5_i(),this.cancel_i()];
	}
	var _proto = AutoPanelSkin.prototype;

	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto.sc_i = function () {
		var t = new eui.Group();
		this.sc = t;
		t.anchorOffsetY = 0;
		t.bottom = 0;
		t.height = 384;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.width = 600;
		t.elementsContent = [this._Rect2_i(),this._Rect3_i(),this._Rect4_i(),this._Rect5_i()];
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 10;
		t.fillColor = 0xb4712f;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 3;
		t.ellipseWidth = 10;
		t.fillColor = 0xf1d193;
		t.left = 3;
		t.right = 3;
		t.top = 3;
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.ellipseWidth = 30;
		t.fillColor = 0xf2debc;
		t.left = 10;
		t.right = 10;
		t.top = 10;
		return t;
	};
	_proto._Rect5_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.fillColor = 0xe2b891;
		t.left = 10;
		t.right = 10;
		t.top = 60;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 320;
		t.height = 154;
		t.horizontalCenter = 0;
		t.source = "sprite-dialog_json.sprite-dialog001";
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.bottom = 385;
		t.horizontalCenter = 0;
		t.text = "自动战斗中";
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		t.bottom = 40;
		t.height = 276;
		t.horizontalCenter = 0;
		t.width = 580;
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this._Group1_i(),this._Group2_i(),this._Group3_i(),this._Group4_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.gap = 30;
		t.horizontalAlign = "left";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.height = 50;
		t.percentWidth = 100;
		t.x = 35;
		t.y = 21;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this._Label2_i(),this.winMoney_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 35;
		t.horizontalAlign = "left";
		t.paddingLeft = 100;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.text = "目标掠夺金豆：";
		t.textAlign = "center";
		t.textColor = 0xce7939;
		t.verticalAlign = "middle";
		t.x = 29;
		t.y = 7;
		return t;
	};
	_proto.winMoney_i = function () {
		var t = new eui.Label();
		this.winMoney = t;
		t.background = true;
		t.backgroundColor = 0xf2debc;
		t.height = 50;
		t.text = "0";
		t.textAlign = "center";
		t.verticalAlign = "middle";
		t.width = 100;
		t.x = 291;
		t.y = 7;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.height = 50;
		t.percentWidth = 100;
		t.x = 45;
		t.y = 31;
		t.layout = this._HorizontalLayout2_i();
		t.elementsContent = [this._Label3_i(),this.loseMoney_i()];
		return t;
	};
	_proto._HorizontalLayout2_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "left";
		t.paddingLeft = 100;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label3_i = function () {
		var t = new eui.Label();
		t.text = "目标被掠夺金豆：";
		t.textAlign = "center";
		t.textColor = 0xCE7939;
		t.verticalAlign = "middle";
		t.x = 29;
		t.y = 7;
		return t;
	};
	_proto.loseMoney_i = function () {
		var t = new eui.Label();
		this.loseMoney = t;
		t.background = true;
		t.backgroundColor = 0xF2DEBC;
		t.height = 50;
		t.text = "0";
		t.textAlign = "center";
		t.verticalAlign = "middle";
		t.width = 100;
		t.x = 291;
		t.y = 7;
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.height = 50;
		t.percentWidth = 100;
		t.x = 55;
		t.y = 41;
		t.layout = this._HorizontalLayout3_i();
		t.elementsContent = [this._Label4_i(),this.NowWinMoney_i()];
		return t;
	};
	_proto._HorizontalLayout3_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 35;
		t.horizontalAlign = "left";
		t.paddingLeft = 100;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label4_i = function () {
		var t = new eui.Label();
		t.text = "当前掠夺金豆：";
		t.textAlign = "center";
		t.textColor = 0xe23b3b;
		t.verticalAlign = "middle";
		t.x = 29;
		t.y = 7;
		return t;
	};
	_proto.NowWinMoney_i = function () {
		var t = new eui.Label();
		this.NowWinMoney = t;
		t.background = true;
		t.backgroundColor = 0xe23b3b;
		t.height = 50;
		t.text = "0";
		t.textAlign = "center";
		t.verticalAlign = "middle";
		t.width = 100;
		t.x = 291;
		t.y = 7;
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		t.height = 50;
		t.percentWidth = 100;
		t.x = 65;
		t.y = 51;
		t.layout = this._HorizontalLayout4_i();
		t.elementsContent = [this._Label5_i(),this.NowLoseMoney_i()];
		return t;
	};
	_proto._HorizontalLayout4_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "left";
		t.paddingLeft = 100;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Label5_i = function () {
		var t = new eui.Label();
		t.text = "当前被掠夺金豆：";
		t.textAlign = "center";
		t.textColor = 0xe23b3b;
		t.verticalAlign = "middle";
		t.x = 29;
		t.y = 7;
		return t;
	};
	_proto.NowLoseMoney_i = function () {
		var t = new eui.Label();
		this.NowLoseMoney = t;
		t.background = true;
		t.backgroundColor = 0xe23b3b;
		t.height = 50;
		t.text = "0";
		t.textAlign = "center";
		t.verticalAlign = "middle";
		t.width = 100;
		t.x = 291;
		t.y = 7;
		return t;
	};
	_proto.cancel_i = function () {
		var t = new eui.Button();
		this.cancel = t;
		t.height = 80;
		t.label = "";
		t.left = 20;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 120;
		t.width = 80;
		t.skinName = AutoPanelSkin$Skin3;
		return t;
	};
	return AutoPanelSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/BetHistorySkin.exml'] = window.BetHistorySkin = (function (_super) {
	__extends(BetHistorySkin, _super);
	function BetHistorySkin() {
		_super.call(this);
		this.skinParts = ["back","sc","back1","scrollerList","loading","more","scroller","zero"];
		
		this.height = 1136;
		this.width = 640;
		this.elementsContent = [this.back_i(),this._Group2_i()];
	}
	var _proto = BetHistorySkin.prototype;

	_proto.back_i = function () {
		var t = new eui.Rect();
		this.back = t;
		t.bottom = 0;
		t.fillAlpha = 0;
		t.fillColor = 0x7f2626;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.anchorOffsetY = 0;
		t.height = 750;
		t.horizontalCenter = 0;
		t.verticalCenter = -20;
		t.width = 640;
		t.elementsContent = [this.sc_i(),this._Image1_i(),this.back1_i(),this._Rect6_i(),this.scroller_i(),this._Label1_i(),this.zero_i()];
		return t;
	};
	_proto.sc_i = function () {
		var t = new eui.Group();
		this.sc = t;
		t.anchorOffsetY = 0;
		t.height = 649;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 100;
		t.width = 640;
		t.x = 0;
		t.y = 120;
		t.elementsContent = [this._Rect1_i(),this._Rect2_i(),this._Rect3_i(),this._Rect4_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 10;
		t.fillColor = 0xb4712f;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 3;
		t.ellipseWidth = 10;
		t.fillColor = 0xf1d193;
		t.left = 3;
		t.right = 3;
		t.top = 3;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.ellipseWidth = 30;
		t.fillColor = 0xf2debc;
		t.left = 10;
		t.right = 10;
		t.top = 10;
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.fillColor = 0xe2b891;
		t.left = 10;
		t.right = 10;
		t.top = 60;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-dialog_json.sprite-dialog001";
		t.top = 0;
		return t;
	};
	_proto.back1_i = function () {
		var t = new eui.Group();
		this.back1 = t;
		t.height = 40;
		t.width = 40;
		t.x = 531;
		t.y = 67;
		t.elementsContent = [this._Rect5_i(),this._Image2_i()];
		return t;
	};
	_proto._Rect5_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 40;
		t.fillColor = 0xf78d02;
		t.left = 0;
		t.right = 0;
		t.strokeColor = 0x845625;
		t.strokeWeight = 3;
		t.top = 0;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "sprite-dialog_json.sprite-dialog000";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Rect6_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x36e2e2;
		t.height = 5;
		t.horizontalCenter = 0;
		t.top = 155;
		t.width = 620;
		return t;
	};
	_proto.scroller_i = function () {
		var t = new eui.Scroller();
		this.scroller = t;
		t.anchorOffsetY = 0;
		t.bottom = 13;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 160;
		t.width = 620;
		t.viewport = this._Group1_i();
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this.scrollerList_i(),this.more_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		return t;
	};
	_proto.scrollerList_i = function () {
		var t = new eui.List();
		this.scrollerList = t;
		t.horizontalCenter = 0;
		t.itemRendererSkinName = BetItemSkin;
		t.top = 0;
		t.percentWidth = 100;
		return t;
	};
	_proto.more_i = function () {
		var t = new eui.Group();
		this.more = t;
		t.bottom = 0;
		t.height = 40;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.width = 620;
		t.elementsContent = [this._Rect7_i(),this.loading_i()];
		return t;
	};
	_proto._Rect7_i = function () {
		var t = new eui.Rect();
		t.percentHeight = 100;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.percentWidth = 100;
		return t;
	};
	_proto.loading_i = function () {
		var t = new eui.Label();
		this.loading = t;
		t.percentHeight = 100;
		t.horizontalCenter = 0;
		t.size = 20;
		t.text = "正在加载中。。。";
		t.textAlign = "center";
		t.textColor = 0xa48aef;
		t.verticalAlign = "middle";
		t.verticalCenter = 0;
		t.percentWidth = 100;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.text = "对局记录";
		t.top = 60;
		return t;
	};
	_proto.zero_i = function () {
		var t = new eui.Label();
		this.zero = t;
		t.horizontalCenter = 0;
		t.text = "暂无记录哦！";
		t.textColor = 0x381a1a;
		t.visible = false;
		t.y = 241;
		return t;
	};
	return BetHistorySkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/BetItemSkin.exml'] = window.BetItemSkin = (function (_super) {
	__extends(BetItemSkin, _super);
	function BetItemSkin() {
		_super.call(this);
		this.skinParts = ["betting","enemy_betting","soldier","line","result","create_time","money"];
		
		this.height = 150;
		this.width = 620;
		this.elementsContent = [this.soldier_i(),this.line_i(),this.result_i(),this.create_time_i(),this.money_i()];
	}
	var _proto = BetItemSkin.prototype;

	_proto.soldier_i = function () {
		var t = new eui.Group();
		this.soldier = t;
		t.height = 30;
		t.left = 25;
		t.top = 30;
		t.width = 220;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this.betting_i(),this._Label1_i(),this.enemy_betting_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 6;
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto.betting_i = function () {
		var t = new eui.Label();
		this.betting = t;
		t.bold = true;
		t.height = 30;
		t.scaleX = 1;
		t.scaleY = 1;
		t.text = "矛兵";
		t.textColor = 0x4a2d15;
		t.verticalAlign = "middle";
		t.x = 4;
		t.y = -6;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.bold = true;
		t.italic = true;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 20;
		t.text = "VS";
		t.textColor = 0x4a2d15;
		t.x = 87;
		t.y = -5;
		return t;
	};
	_proto.enemy_betting_i = function () {
		var t = new eui.Label();
		this.enemy_betting = t;
		t.bold = true;
		t.height = 30;
		t.scaleX = 1;
		t.scaleY = 1;
		t.text = "矛兵";
		t.textColor = 0x4a2d15;
		t.x = 138;
		t.y = -5;
		return t;
	};
	_proto.line_i = function () {
		var t = new eui.Group();
		this.line = t;
		t.bottom = 0;
		t.height = 3;
		t.horizontalCenter = 0;
		t.width = 620;
		t.elementsContent = [this._Rect1_i(),this._Rect2_i(),this._Rect3_i(),this._Rect4_i(),this._Rect5_i(),this._Rect6_i(),this._Rect7_i(),this._Rect8_i(),this._Rect9_i(),this._Rect10_i(),this._Rect11_i(),this._Rect12_i(),this._Rect13_i(),this._Rect14_i(),this._Rect15_i(),this._Rect16_i(),this._Rect17_i(),this._Rect18_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 50;
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 80;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 110;
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 140;
		return t;
	};
	_proto._Rect5_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 170;
		return t;
	};
	_proto._Rect6_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 200;
		return t;
	};
	_proto._Rect7_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 230;
		return t;
	};
	_proto._Rect8_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 260;
		return t;
	};
	_proto._Rect9_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 290;
		return t;
	};
	_proto._Rect10_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 320;
		return t;
	};
	_proto._Rect11_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 350;
		return t;
	};
	_proto._Rect12_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 380;
		return t;
	};
	_proto._Rect13_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 410;
		return t;
	};
	_proto._Rect14_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 440;
		return t;
	};
	_proto._Rect15_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 470;
		return t;
	};
	_proto._Rect16_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 500;
		return t;
	};
	_proto._Rect17_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 530;
		return t;
	};
	_proto._Rect18_i = function () {
		var t = new eui.Rect();
		t.fillColor = 0x808080;
		t.height = 3;
		t.verticalCenter = 0;
		t.width = 20;
		t.x = 560;
		return t;
	};
	_proto.result_i = function () {
		var t = new eui.Label();
		this.result = t;
		t.right = 30;
		t.text = "胜+290金豆";
		t.textAlign = "right";
		t.textColor = 0x4a2d15;
		t.top = 30;
		t.width = 300;
		return t;
	};
	_proto.create_time_i = function () {
		var t = new eui.Label();
		this.create_time = t;
		t.bottom = 20;
		t.left = 55;
		t.text = "2018-02-08 17:35:17";
		t.textColor = 0x4f4747;
		t.width = 300;
		return t;
	};
	_proto.money_i = function () {
		var t = new eui.Label();
		this.money = t;
		t.bottom = 20;
		t.right = 30;
		t.text = "投注200金豆";
		t.textAlign = "right";
		t.textColor = 0x4f4747;
		t.width = 200;
		return t;
	};
	return BetItemSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/CodeItemSkin.exml'] = window.CodeItemSkin = (function (_super) {
	__extends(CodeItemSkin, _super);
	function CodeItemSkin() {
		_super.call(this);
		this.skinParts = ["txt"];
		
		this.height = 65;
		this.minHeight = 50;
		this.minWidth = 100;
		this.width = 200;
		this.elementsContent = [this._Group2_i()];
		this.states = [
			new eui.State ("up",
				[
					new eui.SetProperty("_Image1","visible",false)
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
	var _proto = CodeItemSkin.prototype;

	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.elementsContent = [this._Rect1_i(),this._Image1_i(),this._Group1_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillColor = 0x915128;
		t.height = 2;
		t.horizontalCenter = 0;
		t.percentWidth = 80;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		this._Image1 = t;
		t.left = 25;
		t.source = "gougou_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.right = 20;
		t.verticalCenter = 0;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this._Image2_i(),this.txt_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.height = 20;
		t.horizontalCenter = 0;
		t.scaleX = 1.5;
		t.scaleY = 1.5;
		t.source = "sprite-game_json.sprite-game000";
		t.verticalCenter = 0;
		t.width = 20;
		return t;
	};
	_proto.txt_i = function () {
		var t = new eui.Label();
		this.txt = t;
		t.right = 0;
		t.text = "50000";
		t.verticalCenter = 0;
		return t;
	};
	return CodeItemSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/CodeListSkin.exml'] = window.CodeListSkin = (function (_super) {
	__extends(CodeListSkin, _super);
	function CodeListSkin() {
		_super.call(this);
		this.skinParts = ["show","back","_codeList","group"];
		
		this.height = 1136;
		this.width = 640;
		this.show_i();
		this.elementsContent = [this.back_i(),this.group_i()];
		
		eui.Binding.$bindProperties(this, ["group"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object1,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object1,"height")
		eui.Binding.$bindProperties(this, [1],[],this._Object2,"alpha")
		eui.Binding.$bindProperties(this, [384],[],this._Object2,"height")
	}
	var _proto = CodeListSkin.prototype;

	_proto.show_i = function () {
		var t = new egret.tween.TweenGroup();
		this.show = t;
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
		t.duration = 250;
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
		t.bottom = 80;
		t.fillAlpha = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto.group_i = function () {
		var t = new eui.Group();
		this.group = t;
		t.bottom = 80;
		t.height = 384;
		t.horizontalCenter = 0;
		t.width = 220;
		t.elementsContent = [this._Image1_i(),this._codeList_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.scale9Grid = new egret.Rectangle(82,30,156,8);
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "inputImg_png";
		t.top = 0;
		t.x = 0;
		t.y = 0;
		return t;
	};
	_proto._codeList_i = function () {
		var t = new eui.List();
		this._codeList = t;
		t.bottom = 0;
		t.height = 384;
		t.itemRendererSkinName = CodeItemSkin;
		t.left = 0;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 0;
		t.x = 0;
		t.y = 0;
		return t;
	};
	return CodeListSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/ExplainSkin.exml'] = window.ExplainSkin = (function (_super) {
	__extends(ExplainSkin, _super);
	function ExplainSkin() {
		_super.call(this);
		this.skinParts = ["back","sc","back1","scroller"];
		
		this.height = 1136;
		this.width = 640;
		this.elementsContent = [this._Group6_i()];
	}
	var _proto = ExplainSkin.prototype;

	_proto._Group6_i = function () {
		var t = new eui.Group();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		t.width = 640;
		t.elementsContent = [this.back_i(),this.sc_i(),this._Image1_i(),this._Label1_i(),this.back1_i(),this.scroller_i()];
		return t;
	};
	_proto.back_i = function () {
		var t = new eui.Rect();
		this.back = t;
		t.bottom = 0;
		t.fillAlpha = 0;
		t.left = 0;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 0;
		t.x = 0;
		t.y = 0;
		return t;
	};
	_proto.sc_i = function () {
		var t = new eui.Group();
		this.sc = t;
		t.bottom = 150;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 250;
		t.width = 600;
		t.x = 0;
		t.elementsContent = [this._Rect1_i(),this._Rect2_i(),this._Rect3_i(),this._Rect4_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 10;
		t.fillColor = 0xb4712f;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 3;
		t.ellipseWidth = 10;
		t.fillColor = 0xf1d193;
		t.left = 3;
		t.right = 3;
		t.top = 3;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.ellipseWidth = 30;
		t.fillColor = 0xf2debc;
		t.left = 10;
		t.right = 10;
		t.top = 10;
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.bottom = 10;
		t.fillColor = 0xe2b891;
		t.left = 10;
		t.right = 10;
		t.top = 60;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "sprite-dialog_json.sprite-dialog001";
		t.top = 150;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = 0;
		t.text = "玩法说明";
		t.top = 210;
		return t;
	};
	_proto.back1_i = function () {
		var t = new eui.Group();
		this.back1 = t;
		t.height = 40;
		t.right = 70;
		t.top = 210;
		t.width = 40;
		t.elementsContent = [this._Rect5_i(),this._Image2_i()];
		return t;
	};
	_proto._Rect5_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 40;
		t.fillColor = 0xf78d02;
		t.left = 0;
		t.right = 0;
		t.strokeColor = 0x845625;
		t.strokeWeight = 3;
		t.top = 0;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "sprite-dialog_json.sprite-dialog000";
		t.verticalCenter = 0;
		return t;
	};
	_proto.scroller_i = function () {
		var t = new eui.Scroller();
		this.scroller = t;
		t.bottom = 160;
		t.horizontalCenter = 0;
		t.top = 320;
		t.width = 580;
		t.viewport = this._Group5_i();
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this._Image3_i(),this._Group2_i(),this._Group4_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.gap = 20;
		t.horizontalAlign = "center";
		t.verticalAlign = "top";
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "sprite-dialog_json.sprite-dialog003";
		t.top = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.width = 520;
		t.x = 186;
		t.y = 206;
		t.elementsContent = [this._Group1_i(),this._Label3_i()];
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.left = 0;
		t.top = 0;
		t.elementsContent = [this._Rect6_i(),this._Label2_i()];
		return t;
	};
	_proto._Rect6_i = function () {
		var t = new eui.Rect();
		t.ellipseHeight = 0;
		t.ellipseWidth = 20;
		t.fillColor = 0xd17727;
		t.height = 20;
		t.left = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 5;
		t.width = 20;
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.bold = true;
		t.left = 40;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 35;
		t.text = "玩法说明：";
		t.textColor = 0x487227;
		t.top = 0;
		return t;
	};
	_proto._Label3_i = function () {
		var t = new eui.Label();
		t.left = 50;
		t.text = "选择兵种和人数，招募士兵出战，\n每招募100士兵消耗100金豆粮草,\n战胜或者打平获得一定数量奖励，\n打输没有奖励。";
		t.textColor = 0xd17727;
		t.top = 50;
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		t.width = 520;
		t.x = 196;
		t.y = 216;
		t.elementsContent = [this._Group3_i(),this._Label5_i(),this._Label6_i(),this._Label7_i(),this._Label8_i()];
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.left = 0;
		t.top = 0;
		t.elementsContent = [this._Rect7_i(),this._Label4_i()];
		return t;
	};
	_proto._Rect7_i = function () {
		var t = new eui.Rect();
		t.ellipseHeight = 0;
		t.ellipseWidth = 20;
		t.fillColor = 0xD17727;
		t.height = 20;
		t.left = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 5;
		t.width = 20;
		return t;
	};
	_proto._Label4_i = function () {
		var t = new eui.Label();
		t.left = 40;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 35;
		t.text = "自动战斗：";
		t.textColor = 0x487227;
		t.top = 0;
		return t;
	};
	_proto._Label5_i = function () {
		var t = new eui.Label();
		t.left = 50;
		t.text = "掠夺金豆：自动战斗时，当掠夺的\n金豆数量大于等于你设置的掠夺数\n量时,退出自动战斗。";
		t.textColor = 0xD17727;
		t.top = 50;
		return t;
	};
	_proto._Label6_i = function () {
		var t = new eui.Label();
		t.left = 50;
		t.text = "被掠夺金豆：自动战斗时，当被掠\n夺的金豆数量大于等于你设置的金\n豆数量时，退出自动战斗。";
		t.textColor = 0xd17727;
		t.top = 150;
		return t;
	};
	_proto._Label7_i = function () {
		var t = new eui.Label();
		t.left = 50;
		t.text = "单局出兵：自动战斗时，每局出兵\n的数量。";
		t.textColor = 0xD17727;
		t.top = 250;
		return t;
	};
	_proto._Label8_i = function () {
		var t = new eui.Label();
		t.left = 50;
		t.text = "自动出兵：随机，每局随机选择一\n种士兵出战。不随机则每局出战士\n兵为你选择的士兵。";
		t.textColor = 0xD17727;
		t.top = 320;
		return t;
	};
	return ExplainSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/FightSkin.exml'] = window.FightSkin = (function (_super) {
	__extends(FightSkin, _super);
	function FightSkin() {
		_super.call(this);
		this.skinParts = [];
		
		this.height = 100;
		this.width = 250;
		this.elementsContent = [this._Rect1_i(),this._Image1_i(),this._Image2_i(),this._Image3_i()];
	}
	var _proto = FightSkin.prototype;

	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 30;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 2;
		t.left = 2;
		t.right = 2;
		t.scale9Grid = new egret.Rectangle(51,0,71,0);
		t.scaleX = 1.4;
		t.scaleY = 1.4;
		t.source = "btn2_png";
		t.top = 2;
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -50;
		t.source = "sprite-game_json.sprite-game009";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 50;
		t.source = "sprite-game_json.sprite-game008";
		t.verticalCenter = 0;
		return t;
	};
	return FightSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/SoldierSkin.exml'] = window.SoldierSkin = (function (_super) {
	__extends(SoldierSkin, _super);
	function SoldierSkin() {
		_super.call(this);
		this.skinParts = ["loseHarf","loseAll","soldier","life","s_name"];
		
		this.height = 254;
		this.width = 190;
		this.loseHarf_i();
		this.loseAll_i();
		this.elementsContent = [this.soldier_i(),this._Group1_i(),this.s_name_i()];
		
		eui.Binding.$bindProperties(this, ["life"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object1,"x")
		eui.Binding.$bindProperties(this, [75],[],this._Object2,"width")
		eui.Binding.$bindProperties(this, [0],[],this._Object2,"x")
		eui.Binding.$bindProperties(this, ["life"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object3,"x")
		eui.Binding.$bindProperties(this, [0],[],this._Object4,"width")
		eui.Binding.$bindProperties(this, [0],[],this._Object4,"x")
	}
	var _proto = SoldierSkin.prototype;

	_proto.loseHarf_i = function () {
		var t = new egret.tween.TweenGroup();
		this.loseHarf = t;
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
		t.duration = 1000;
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto.loseAll_i = function () {
		var t = new egret.tween.TweenGroup();
		this.loseAll = t;
		t.items = [this._TweenItem2_i()];
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
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto._To2_i = function () {
		var t = new egret.tween.To();
		t.duration = 1500;
		t.props = this._Object4_i();
		return t;
	};
	_proto._Object4_i = function () {
		var t = {};
		this._Object4 = t;
		return t;
	};
	_proto.soldier_i = function () {
		var t = new eui.Image();
		this.soldier = t;
		t.bottom = 77;
		t.horizontalCenter = 0;
		t.source = "sprite-game-soldier_json.sprite-game-soldier000";
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.height = 10;
		t.horizontalCenter = 0;
		t.scrollEnabled = true;
		t.width = 150;
		t.y = 194;
		t.elementsContent = [this._Rect1_i(),this.life_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.ellipseWidth = 16;
		t.fillAlpha = 0;
		t.fillColor = 0xffffff;
		t.height = 10;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.strokeColor = 0xf75151;
		t.strokeWeight = 2;
		t.verticalCenter = 0;
		t.width = 150;
		return t;
	};
	_proto.life_i = function () {
		var t = new eui.Rect();
		this.life = t;
		t.ellipseWidth = 16;
		t.fillColor = 0xF75151;
		t.height = 10;
		t.scaleX = 1;
		t.scaleY = 1;
		t.width = 150;
		t.x = 0;
		t.y = 0;
		return t;
	};
	_proto.s_name_i = function () {
		var t = new eui.Label();
		this.s_name = t;
		t.horizontalCenter = 0;
		t.text = "枪兵";
		t.y = 215;
		return t;
	};
	return SoldierSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/SoldierGroupSkin.exml'] = window.SoldierGroupSkin = (function (_super) {
	__extends(SoldierGroupSkin, _super);
	function SoldierGroupSkin() {
		_super.call(this);
		this.skinParts = ["soldier0","soldier1","soldier2","soldiers","fight"];
		
		this.elementsContent = [this.soldiers_i(),this.fight_i()];
	}
	var _proto = SoldierGroupSkin.prototype;

	_proto.soldiers_i = function () {
		var t = new eui.Group();
		this.soldiers = t;
		t.width = 640;
		t.x = 0;
		t.y = 0;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this.soldier0_i(),this.soldier1_i(),this.soldier2_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto.soldier0_i = function () {
		var t = new Soldier();
		this.soldier0 = t;
		t.currentState = "index";
		t.skinName = "SoldierSkin";
		t.x = 81;
		t.y = 58;
		return t;
	};
	_proto.soldier1_i = function () {
		var t = new Soldier();
		this.soldier1 = t;
		t.skinName = "SoldierSkin";
		t.soldierType = 1;
		t.x = 91;
		t.y = 68;
		return t;
	};
	_proto.soldier2_i = function () {
		var t = new Soldier();
		this.soldier2 = t;
		t.skinName = "SoldierSkin";
		t.soldierType = 2;
		t.x = 101;
		t.y = 78;
		return t;
	};
	_proto.fight_i = function () {
		var t = new Fight();
		this.fight = t;
		t.horizontalCenter = 0;
		t.skinName = "FightSkin";
		t.top = -100;
		t.visible = false;
		return t;
	};
	return SoldierGroupSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/GameUiSkin.exml'] = window.GameUiSkin = (function (_super) {
	__extends(GameUiSkin, _super);
	var GameUiSkin$Skin4 = 	(function (_super) {
		__extends(GameUiSkin$Skin4, _super);
		function GameUiSkin$Skin4() {
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
						new eui.SetProperty("_Image1","source","btn2_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = GameUiSkin$Skin4.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "btn1_png";
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
		return GameUiSkin$Skin4;
	})(eui.Skin);

	var GameUiSkin$Skin5 = 	(function (_super) {
		__extends(GameUiSkin$Skin5, _super);
		function GameUiSkin$Skin5() {
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
						new eui.SetProperty("_Image1","source","btn2_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = GameUiSkin$Skin5.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "btn1_png";
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
		return GameUiSkin$Skin5;
	})(eui.Skin);

	var GameUiSkin$Skin6 = 	(function (_super) {
		__extends(GameUiSkin$Skin6, _super);
		function GameUiSkin$Skin6() {
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
						new eui.SetProperty("_Image1","source","btn2_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = GameUiSkin$Skin6.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "btn1_png";
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
		return GameUiSkin$Skin6;
	})(eui.Skin);

	var GameUiSkin$Skin7 = 	(function (_super) {
		__extends(GameUiSkin$Skin7, _super);
		function GameUiSkin$Skin7() {
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
						new eui.SetProperty("_Image1","source","ready2_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = GameUiSkin$Skin7.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "ready_png";
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
		return GameUiSkin$Skin7;
	})(eui.Skin);

	var GameUiSkin$Skin8 = 	(function (_super) {
		__extends(GameUiSkin$Skin8, _super);
		function GameUiSkin$Skin8() {
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
						new eui.SetProperty("_Image1","source","btn2_png")
					])
				,
				new eui.State ("disabled",
					[
					])
			];
		}
		var _proto = GameUiSkin$Skin8.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			this._Image1 = t;
			t.percentHeight = 100;
			t.source = "btn1_png";
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
		return GameUiSkin$Skin8;
	})(eui.Skin);

	var GameUiSkin$Skin9 = 	(function (_super) {
		__extends(GameUiSkin$Skin9, _super);
		function GameUiSkin$Skin9() {
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
		var _proto = GameUiSkin$Skin9.prototype;

		_proto._Image1_i = function () {
			var t = new eui.Image();
			t.percentHeight = 100;
			t.source = "auto_fight_png";
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
		return GameUiSkin$Skin9;
	})(eui.Skin);

	function GameUiSkin() {
		_super.call(this);
		this.skinParts = ["readyZoo","readyTwo","cityImg","recharge","active","award","set","head","cut","add","codeInpt","btns","ready","all_in","bottom","soldiers","auto_fight","user_img","rec_mask","head_img","user_name","readyFight","timeImg","Count_down","timers","share"];
		
		this.height = 1136;
		this.width = 640;
		this.readyZoo_i();
		this.readyTwo_i();
		this.elementsContent = [this._Image1_i(),this._Group1_i(),this.head_i(),this.bottom_i(),this.soldiers_i(),this._Group5_i(),this._Group7_i(),this.readyFight_i(),this.timers_i(),this.share_i()];
		
		eui.Binding.$bindProperties(this, ["readyFight"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [1],[],this._Object1,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object1,"scaleY")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object2,"scaleX")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object2,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object3,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object3,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object4,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object5,"alpha")
		eui.Binding.$bindProperties(this, ["readyFight"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [1],[],this._Object6,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object6,"scaleY")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object7,"scaleX")
		eui.Binding.$bindProperties(this, [1.2],[],this._Object7,"scaleY")
		eui.Binding.$bindProperties(this, [1],[],this._Object8,"scaleX")
		eui.Binding.$bindProperties(this, [1],[],this._Object8,"scaleY")
	}
	var _proto = GameUiSkin.prototype;

	_proto.readyZoo_i = function () {
		var t = new egret.tween.TweenGroup();
		this.readyZoo = t;
		t.items = [this._TweenItem1_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Set1_i(),this._To1_i(),this._To2_i(),this._Wait1_i(),this._Set2_i(),this._To3_i()];
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
		t.duration = 500;
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto._Wait1_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 500;
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
		t.duration = 500;
		t.props = this._Object5_i();
		return t;
	};
	_proto._Object5_i = function () {
		var t = {};
		this._Object5 = t;
		return t;
	};
	_proto.readyTwo_i = function () {
		var t = new egret.tween.TweenGroup();
		this.readyTwo = t;
		t.items = [this._TweenItem2_i()];
		return t;
	};
	_proto._TweenItem2_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem2 = t;
		t.paths = [this._Set3_i(),this._To4_i(),this._To5_i()];
		return t;
	};
	_proto._Set3_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object6_i();
		return t;
	};
	_proto._Object6_i = function () {
		var t = {};
		this._Object6 = t;
		return t;
	};
	_proto._To4_i = function () {
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
	_proto._To5_i = function () {
		var t = new egret.tween.To();
		t.duration = 500;
		t.props = this._Object8_i();
		return t;
	};
	_proto._Object8_i = function () {
		var t = {};
		this._Object8 = t;
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "backImg_jpg";
		t.top = 0;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.anchorOffsetY = 0;
		t.bottom = 140;
		t.height = 711;
		t.horizontalCenter = 0;
		t.elementsContent = [this._Image2_i(),this.cityImg_i()];
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "bg-table_json.bg-table000";
		return t;
	};
	_proto.cityImg_i = function () {
		var t = new eui.Image();
		this.cityImg = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-defense__170112_json.sprite-defense__170112002";
		return t;
	};
	_proto.head_i = function () {
		var t = new eui.Group();
		this.head = t;
		t.height = 100;
		t.horizontalCenter = 0;
		t.top = 0;
		t.width = 640;
		t.elementsContent = [this._Rect1_i(),this._Group2_i(),this._Group3_i(),this._Image5_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillColor = 0x55331b;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.height = 50;
		t.left = 30;
		t.verticalCenter = 0;
		t.width = 180;
		t.elementsContent = [this._Rect2_i(),this._Image3_i(),this._Image4_i(),this.recharge_i()];
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.anchorOffsetX = 0;
		t.ellipseWidth = 50;
		t.fillColor = 0x2D1A05;
		t.percentHeight = 90;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.verticalCenter = 0;
		t.percentWidth = 100;
		t.y = 4.999999999999773;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-game_json.sprite-game001";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.left = 0;
		t.scaleX = 1.5;
		t.scaleY = 1.5;
		t.source = "sprite-game_json.sprite-game000";
		t.verticalCenter = 0;
		return t;
	};
	_proto.recharge_i = function () {
		var t = new eui.Label();
		this.recharge = t;
		t.anchorOffsetX = 0;
		t.height = 50;
		t.horizontalCenter = 6;
		t.size = 25;
		t.text = "0";
		t.textAlign = "left";
		t.textColor = 0xEAA215;
		t.verticalAlign = "middle";
		t.verticalCenter = 0;
		t.percentWidth = 70;
		return t;
	};
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.anchorOffsetX = 0;
		t.height = 100;
		t.width = 239.39;
		t.x = 399.61;
		t.y = 0;
		t.layout = this._HorizontalLayout1_i();
		t.elementsContent = [this.active_i(),this.award_i(),this.set_i()];
		return t;
	};
	_proto._HorizontalLayout1_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 25;
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto.active_i = function () {
		var t = new eui.Image();
		this.active = t;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite_json.sprite000";
		t.visible = false;
		t.x = 186.38999999999993;
		t.y = 20;
		return t;
	};
	_proto.award_i = function () {
		var t = new eui.Image();
		this.award = t;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite_json.sprite001";
		t.visible = false;
		t.x = 185.38999999999993;
		t.y = 22.000000000000004;
		return t;
	};
	_proto.set_i = function () {
		var t = new eui.Image();
		this.set = t;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite_json.sprite002";
		t.x = 186.38999999999993;
		t.y = 23.000000000000004;
		return t;
	};
	_proto._Image5_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 1.5;
		t.scaleY = 1.5;
		t.source = "sprite-game-v170726012_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.bottom_i = function () {
		var t = new eui.Group();
		this.bottom = t;
		t.anchorOffsetY = 0;
		t.bottom = 0;
		t.height = 140;
		t.horizontalCenter = 0;
		t.width = 640;
		t.elementsContent = [this._Rect3_i(),this._Rect4_i(),this._Label1_i(),this._Group4_i(),this.btns_i(),this.ready_i(),this.all_in_i()];
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillColor = 0x93754f;
		t.left = 0;
		t.right = 0;
		t.top = 0;
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.anchorOffsetY = 0;
		t.bottom = 0;
		t.fillColor = 0x563d16;
		t.percentHeight = 95;
		t.horizontalCenter = 0;
		t.percentWidth = 100;
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = 0;
		t.size = 30;
		t.text = "每出兵100人，消耗100金豆粮草";
		t.textColor = 0xad7835;
		t.top = 22;
		return t;
	};
	_proto._Group4_i = function () {
		var t = new eui.Group();
		t.bottom = 11;
		t.height = 64;
		t.horizontalCenter = 0;
		t.width = 316;
		t.elementsContent = [this._Image6_i(),this.cut_i(),this.add_i(),this.codeInpt_i()];
		return t;
	};
	_proto._Image6_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "inputImg_png";
		t.verticalCenter = 0;
		t.x = 60;
		t.y = -27;
		return t;
	};
	_proto.cut_i = function () {
		var t = new eui.Image();
		this.cut = t;
		t.left = 0;
		t.source = "cut_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.add_i = function () {
		var t = new eui.Image();
		this.add = t;
		t.right = 0;
		t.source = "add_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.codeInpt_i = function () {
		var t = new eui.Label();
		this.codeInpt = t;
		t.bottom = 10;
		t.left = 50;
		t.right = 50;
		t.text = "";
		t.textAlign = "center";
		t.top = 10;
		t.verticalAlign = "middle";
		return t;
	};
	_proto.btns_i = function () {
		var t = new eui.Group();
		this.btns = t;
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.bottom = 0;
		t.height = 80;
		t.horizontalCenter = -60;
		t.visible = false;
		t.width = 420;
		t.layout = this._HorizontalLayout2_i();
		t.elementsContent = [this._RadioButton1_i(),this._RadioButton2_i(),this._RadioButton3_i()];
		return t;
	};
	_proto._HorizontalLayout2_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 15;
		t.horizontalAlign = "center";
		t.paddingLeft = 0;
		t.verticalAlign = "middle";
		return t;
	};
	_proto._RadioButton1_i = function () {
		var t = new eui.RadioButton();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 70;
		t.label = "100";
		t.scaleX = 1;
		t.scaleY = 1;
		t.width = 120;
		t.x = -66;
		t.y = -365.6600000000001;
		t.skinName = GameUiSkin$Skin4;
		return t;
	};
	_proto._RadioButton2_i = function () {
		var t = new eui.RadioButton();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 70;
		t.label = "500";
		t.scaleX = 1;
		t.scaleY = 1;
		t.width = 120;
		t.x = -56;
		t.y = -355.6600000000001;
		t.skinName = GameUiSkin$Skin5;
		return t;
	};
	_proto._RadioButton3_i = function () {
		var t = new eui.RadioButton();
		t.anchorOffsetX = 0;
		t.anchorOffsetY = 0;
		t.height = 70;
		t.label = "1000";
		t.scaleX = 1;
		t.scaleY = 1;
		t.width = 120;
		t.x = -46;
		t.y = -345.6600000000001;
		t.skinName = GameUiSkin$Skin6;
		return t;
	};
	_proto.ready_i = function () {
		var t = new eui.ToggleButton();
		this.ready = t;
		t.bottom = 5;
		t.height = 70;
		t.label = "";
		t.right = 30;
		t.scaleX = 1;
		t.scaleY = 1;
		t.width = 120;
		t.skinName = GameUiSkin$Skin7;
		return t;
	};
	_proto.all_in_i = function () {
		var t = new eui.Group();
		this.all_in = t;
		t.bottom = 5;
		t.height = 70;
		t.left = 30;
		t.scaleX = 1;
		t.scaleY = 1;
		t.width = 120;
		t.y = 10;
		t.elementsContent = [this._Button1_i(),this._Label2_i()];
		return t;
	};
	_proto._Button1_i = function () {
		var t = new eui.Button();
		t.height = 70;
		t.horizontalCenter = 0;
		t.label = "";
		t.scaleX = 1;
		t.scaleY = 1;
		t.verticalCenter = 0;
		t.width = 120;
		t.skinName = GameUiSkin$Skin8;
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.horizontalCenter = 0;
		t.size = 30;
		t.text = "全压";
		t.textColor = 0xFFFFFF;
		t.touchEnabled = false;
		t.verticalCenter = 0;
		return t;
	};
	_proto.soldiers_i = function () {
		var t = new SoldierGroup();
		this.soldiers = t;
		t.alpha = 0.5;
		t.bottom = 230;
		t.horizontalCenter = 0;
		t.skinName = "SoldierGroupSkin";
		return t;
	};
	_proto._Group5_i = function () {
		var t = new eui.Group();
		t.left = 20;
		t.top = 120;
		t.layout = this._HorizontalLayout3_i();
		t.elementsContent = [this.auto_fight_i()];
		return t;
	};
	_proto._HorizontalLayout3_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto.auto_fight_i = function () {
		var t = new eui.ToggleButton();
		this.auto_fight = t;
		t.height = 80;
		t.label = "";
		t.left = 20;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 120;
		t.visible = false;
		t.width = 80;
		t.x = -167;
		t.y = -44;
		t.skinName = GameUiSkin$Skin9;
		return t;
	};
	_proto._Group7_i = function () {
		var t = new eui.Group();
		t.horizontalCenter = 0;
		t.top = 140;
		t.layout = this._HorizontalLayout4_i();
		t.elementsContent = [this.head_img_i(),this._Group6_i()];
		return t;
	};
	_proto._HorizontalLayout4_i = function () {
		var t = new eui.HorizontalLayout();
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto.head_img_i = function () {
		var t = new eui.Group();
		this.head_img = t;
		t.height = 70;
		t.left = 0;
		t.scrollEnabled = false;
		t.top = 0;
		t.width = 70;
		t.elementsContent = [this._Rect5_i(),this.user_img_i(),this.rec_mask_i()];
		return t;
	};
	_proto._Rect5_i = function () {
		var t = new eui.Rect();
		t.ellipseWidth = 70;
		t.fillAlpha = 1;
		t.fillColor = 0xdb8f13;
		t.percentHeight = 100;
		t.horizontalCenter = 0;
		t.strokeColor = 0x25aace;
		t.strokeWeight = 0;
		t.verticalCenter = 0;
		t.percentWidth = 100;
		return t;
	};
	_proto.user_img_i = function () {
		var t = new eui.Image();
		this.user_img = t;
		t.height = 60;
		t.horizontalCenter = 0;
		t.source = "";
		t.verticalCenter = 0;
		t.width = 60;
		return t;
	};
	_proto.rec_mask_i = function () {
		var t = new eui.Rect();
		this.rec_mask = t;
		t.ellipseWidth = 60;
		t.fillColor = 0x51340c;
		t.height = 60;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.width = 60;
		return t;
	};
	_proto._Group6_i = function () {
		var t = new eui.Group();
		t.x = 133;
		t.y = 16;
		t.elementsContent = [this._Image7_i(),this.user_name_i()];
		return t;
	};
	_proto._Image7_i = function () {
		var t = new eui.Image();
		t.bottom = 0;
		t.left = 0;
		t.right = 0;
		t.source = "user_name_png";
		t.top = 0;
		return t;
	};
	_proto.user_name_i = function () {
		var t = new eui.Label();
		this.user_name = t;
		t.anchorOffsetX = 0;
		t.height = 50;
		t.horizontalCenter = 11.5;
		t.scaleX = 1;
		t.scaleY = 1;
		t.size = 25;
		t.text = "等待加入";
		t.textAlign = "left";
		t.textColor = 0xff8c00;
		t.verticalAlign = "middle";
		t.verticalCenter = 5;
		t.width = 177;
		return t;
	};
	_proto.readyFight_i = function () {
		var t = new eui.Label();
		this.readyFight = t;
		t.bold = true;
		t.bottom = 650;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.text = "已准备，等待敌方出兵！";
		t.textColor = 0xf21a1a;
		t.visible = false;
		return t;
	};
	_proto.timers_i = function () {
		var t = new eui.Group();
		this.timers = t;
		t.bottom = 600;
		t.horizontalCenter = 0;
		t.visible = false;
		t.layout = this._HorizontalLayout5_i();
		t.elementsContent = [this.timeImg_i(),this.Count_down_i()];
		return t;
	};
	_proto._HorizontalLayout5_i = function () {
		var t = new eui.HorizontalLayout();
		t.gap = 15;
		t.horizontalAlign = "center";
		t.verticalAlign = "middle";
		return t;
	};
	_proto.timeImg_i = function () {
		var t = new eui.Image();
		this.timeImg = t;
		t.source = "Count_down_png";
		t.x = 61;
		t.y = 73;
		return t;
	};
	_proto.Count_down_i = function () {
		var t = new eui.BitmapLabel();
		this.Count_down = t;
		t.font = "timeNumbers_fnt";
		t.scaleX = 1.5;
		t.scaleY = 1.5;
		t.text = "";
		t.x = 63;
		t.y = 114;
		return t;
	};
	_proto.share_i = function () {
		var t = new eui.Image();
		this.share = t;
		t.height = 40;
		t.right = 90;
		t.source = "share3_png";
		t.top = 30;
		t.width = 40;
		return t;
	};
	return GameUiSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/InputAddSkin.exml'] = window.InputAddSkin = (function (_super) {
	__extends(InputAddSkin, _super);
	function InputAddSkin() {
		_super.call(this);
		this.skinParts = ["bgImg","textDisplay","promptDisplay"];
		
		this.elementsContent = [this.bgImg_i(),this.textDisplay_i(),this.promptDisplay_i()];
		this.states = [
			new eui.State ("normal",
				[
					new eui.SetProperty("promptDisplay","visible",false)
				])
			,
			new eui.State ("disabled",
				[
					new eui.SetProperty("promptDisplay","visible",false)
				])
			,
			new eui.State ("normalWithPrompt",
				[
				])
			,
			new eui.State ("disabledWithPrompt",
				[
				])
		];
	}
	var _proto = InputAddSkin.prototype;

	_proto.bgImg_i = function () {
		var t = new eui.Image();
		this.bgImg = t;
		t.horizontalCenter = 0;
		t.source = "inputImg_png";
		t.verticalCenter = 0;
		return t;
	};
	_proto.textDisplay_i = function () {
		var t = new eui.EditableText();
		this.textDisplay = t;
		t.bottom = "10";
		t.left = "50";
		t.right = "50";
		t.text = "";
		t.textAlign = "center";
		t.top = "10";
		t.verticalAlign = "middle";
		return t;
	};
	_proto.promptDisplay_i = function () {
		var t = new eui.Label();
		this.promptDisplay = t;
		t.bottom = 10;
		t.left = 50;
		t.right = 50;
		t.text = "";
		t.textAlign = "center";
		t.top = 10;
		t.verticalAlign = "middle";
		return t;
	};
	return InputAddSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/ResultSkin.exml'] = window.ResultSkin = (function (_super) {
	__extends(ResultSkin, _super);
	function ResultSkin() {
		_super.call(this);
		this.skinParts = ["fight","soldier","img0","img1","img2","img3","fightGroup","you","cmp"];
		
		this.height = 1136;
		this.width = 640;
		this.fight_i();
		this.soldier_i();
		this.elementsContent = [this.fightGroup_i(),this.you_i(),this.cmp_i()];
		
		eui.Binding.$bindProperties(this, ["img0"],[0],this._TweenItem1,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object1,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object2,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object3,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object4,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object5,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object6,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object7,"alpha")
		eui.Binding.$bindProperties(this, ["img1"],[0],this._TweenItem2,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object8,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object9,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object10,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object11,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object12,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object13,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object14,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object15,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object16,"alpha")
		eui.Binding.$bindProperties(this, ["img2"],[0],this._TweenItem3,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object17,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object18,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object19,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object20,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object21,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object22,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object23,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object24,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object25,"alpha")
		eui.Binding.$bindProperties(this, ["img3"],[0],this._TweenItem4,"target")
		eui.Binding.$bindProperties(this, [0],[],this._Object26,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object27,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object28,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object29,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object30,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object31,"alpha")
		eui.Binding.$bindProperties(this, [0],[],this._Object32,"alpha")
		eui.Binding.$bindProperties(this, [1],[],this._Object33,"alpha")
		eui.Binding.$bindProperties(this, ["you"],[0],this._TweenItem5,"target")
		eui.Binding.$bindProperties(this, [759],[],this._Object34,"y")
		eui.Binding.$bindProperties(this, [520],[],this._Object35,"y")
		eui.Binding.$bindProperties(this, ["cmp"],[0],this._TweenItem6,"target")
		eui.Binding.$bindProperties(this, [405],[],this._Object36,"y")
		eui.Binding.$bindProperties(this, [520],[],this._Object37,"y")
	}
	var _proto = ResultSkin.prototype;

	_proto.fight_i = function () {
		var t = new egret.tween.TweenGroup();
		this.fight = t;
		t.items = [this._TweenItem1_i(),this._TweenItem2_i(),this._TweenItem3_i(),this._TweenItem4_i()];
		return t;
	};
	_proto._TweenItem1_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem1 = t;
		t.paths = [this._Set1_i(),this._Wait1_i(),this._Set2_i(),this._Wait2_i(),this._Set3_i(),this._Wait3_i(),this._Set4_i(),this._Wait4_i(),this._Set5_i(),this._Wait5_i(),this._Set6_i(),this._Wait6_i(),this._Set7_i(),this._Wait7_i(),this._Set8_i()];
		return t;
	};
	_proto._Set1_i = function () {
		var t = new egret.tween.Set();
		return t;
	};
	_proto._Wait1_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 100;
		return t;
	};
	_proto._Set2_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object1_i();
		return t;
	};
	_proto._Object1_i = function () {
		var t = {};
		this._Object1 = t;
		return t;
	};
	_proto._Wait2_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set3_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object2_i();
		return t;
	};
	_proto._Object2_i = function () {
		var t = {};
		this._Object2 = t;
		return t;
	};
	_proto._Wait3_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set4_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object3_i();
		return t;
	};
	_proto._Object3_i = function () {
		var t = {};
		this._Object3 = t;
		return t;
	};
	_proto._Wait4_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set5_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object4_i();
		return t;
	};
	_proto._Object4_i = function () {
		var t = {};
		this._Object4 = t;
		return t;
	};
	_proto._Wait5_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set6_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object5_i();
		return t;
	};
	_proto._Object5_i = function () {
		var t = {};
		this._Object5 = t;
		return t;
	};
	_proto._Wait6_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set7_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object6_i();
		return t;
	};
	_proto._Object6_i = function () {
		var t = {};
		this._Object6 = t;
		return t;
	};
	_proto._Wait7_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
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
	_proto._TweenItem2_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem2 = t;
		t.paths = [this._Set9_i(),this._Wait8_i(),this._Set10_i(),this._Wait9_i(),this._Set11_i(),this._Wait10_i(),this._Set12_i(),this._Wait11_i(),this._Set13_i(),this._Wait12_i(),this._Set14_i(),this._Wait13_i(),this._Set15_i(),this._Wait14_i(),this._Set16_i(),this._Wait15_i(),this._Set17_i()];
		return t;
	};
	_proto._Set9_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object8_i();
		return t;
	};
	_proto._Object8_i = function () {
		var t = {};
		this._Object8 = t;
		return t;
	};
	_proto._Wait8_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 100;
		return t;
	};
	_proto._Set10_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object9_i();
		return t;
	};
	_proto._Object9_i = function () {
		var t = {};
		this._Object9 = t;
		return t;
	};
	_proto._Wait9_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set11_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object10_i();
		return t;
	};
	_proto._Object10_i = function () {
		var t = {};
		this._Object10 = t;
		return t;
	};
	_proto._Wait10_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set12_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object11_i();
		return t;
	};
	_proto._Object11_i = function () {
		var t = {};
		this._Object11 = t;
		return t;
	};
	_proto._Wait11_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set13_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object12_i();
		return t;
	};
	_proto._Object12_i = function () {
		var t = {};
		this._Object12 = t;
		return t;
	};
	_proto._Wait12_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set14_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object13_i();
		return t;
	};
	_proto._Object13_i = function () {
		var t = {};
		this._Object13 = t;
		return t;
	};
	_proto._Wait13_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set15_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object14_i();
		return t;
	};
	_proto._Object14_i = function () {
		var t = {};
		this._Object14 = t;
		return t;
	};
	_proto._Wait14_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set16_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object15_i();
		return t;
	};
	_proto._Object15_i = function () {
		var t = {};
		this._Object15 = t;
		return t;
	};
	_proto._Wait15_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 100;
		return t;
	};
	_proto._Set17_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object16_i();
		return t;
	};
	_proto._Object16_i = function () {
		var t = {};
		this._Object16 = t;
		return t;
	};
	_proto._TweenItem3_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem3 = t;
		t.paths = [this._Set18_i(),this._Wait16_i(),this._Set19_i(),this._Wait17_i(),this._Set20_i(),this._Wait18_i(),this._Set21_i(),this._Wait19_i(),this._Set22_i(),this._Wait20_i(),this._Set23_i(),this._Wait21_i(),this._Set24_i(),this._Wait22_i(),this._Set25_i(),this._Wait23_i(),this._Set26_i()];
		return t;
	};
	_proto._Set18_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object17_i();
		return t;
	};
	_proto._Object17_i = function () {
		var t = {};
		this._Object17 = t;
		return t;
	};
	_proto._Wait16_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 250;
		return t;
	};
	_proto._Set19_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object18_i();
		return t;
	};
	_proto._Object18_i = function () {
		var t = {};
		this._Object18 = t;
		return t;
	};
	_proto._Wait17_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set20_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object19_i();
		return t;
	};
	_proto._Object19_i = function () {
		var t = {};
		this._Object19 = t;
		return t;
	};
	_proto._Wait18_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set21_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object20_i();
		return t;
	};
	_proto._Object20_i = function () {
		var t = {};
		this._Object20 = t;
		return t;
	};
	_proto._Wait19_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set22_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object21_i();
		return t;
	};
	_proto._Object21_i = function () {
		var t = {};
		this._Object21 = t;
		return t;
	};
	_proto._Wait20_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set23_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object22_i();
		return t;
	};
	_proto._Object22_i = function () {
		var t = {};
		this._Object22 = t;
		return t;
	};
	_proto._Wait21_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set24_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object23_i();
		return t;
	};
	_proto._Object23_i = function () {
		var t = {};
		this._Object23 = t;
		return t;
	};
	_proto._Wait22_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 400;
		return t;
	};
	_proto._Set25_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object24_i();
		return t;
	};
	_proto._Object24_i = function () {
		var t = {};
		this._Object24 = t;
		return t;
	};
	_proto._Wait23_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 100;
		return t;
	};
	_proto._Set26_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object25_i();
		return t;
	};
	_proto._Object25_i = function () {
		var t = {};
		this._Object25 = t;
		return t;
	};
	_proto._TweenItem4_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem4 = t;
		t.paths = [this._Set27_i(),this._Wait24_i(),this._Set28_i(),this._Wait25_i(),this._Set29_i(),this._Wait26_i(),this._Set30_i(),this._Wait27_i(),this._Set31_i(),this._Wait28_i(),this._Set32_i(),this._Wait29_i(),this._Set33_i(),this._Wait30_i(),this._Set34_i()];
		return t;
	};
	_proto._Set27_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object26_i();
		return t;
	};
	_proto._Object26_i = function () {
		var t = {};
		this._Object26 = t;
		return t;
	};
	_proto._Wait24_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 400;
		return t;
	};
	_proto._Set28_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object27_i();
		return t;
	};
	_proto._Object27_i = function () {
		var t = {};
		this._Object27 = t;
		return t;
	};
	_proto._Wait25_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set29_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object28_i();
		return t;
	};
	_proto._Object28_i = function () {
		var t = {};
		this._Object28 = t;
		return t;
	};
	_proto._Wait26_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set30_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object29_i();
		return t;
	};
	_proto._Object29_i = function () {
		var t = {};
		this._Object29 = t;
		return t;
	};
	_proto._Wait27_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set31_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object30_i();
		return t;
	};
	_proto._Object30_i = function () {
		var t = {};
		this._Object30 = t;
		return t;
	};
	_proto._Wait28_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 450;
		return t;
	};
	_proto._Set32_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object31_i();
		return t;
	};
	_proto._Object31_i = function () {
		var t = {};
		this._Object31 = t;
		return t;
	};
	_proto._Wait29_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 150;
		return t;
	};
	_proto._Set33_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object32_i();
		return t;
	};
	_proto._Object32_i = function () {
		var t = {};
		this._Object32 = t;
		return t;
	};
	_proto._Wait30_i = function () {
		var t = new egret.tween.Wait();
		t.duration = 350;
		return t;
	};
	_proto._Set34_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object33_i();
		return t;
	};
	_proto._Object33_i = function () {
		var t = {};
		this._Object33 = t;
		return t;
	};
	_proto.soldier_i = function () {
		var t = new egret.tween.TweenGroup();
		this.soldier = t;
		t.items = [this._TweenItem5_i(),this._TweenItem6_i()];
		return t;
	};
	_proto._TweenItem5_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem5 = t;
		t.paths = [this._Set35_i(),this._To1_i()];
		return t;
	};
	_proto._Set35_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object34_i();
		return t;
	};
	_proto._Object34_i = function () {
		var t = {};
		this._Object34 = t;
		return t;
	};
	_proto._To1_i = function () {
		var t = new egret.tween.To();
		t.duration = 500;
		t.ease = "sineIn";
		t.props = this._Object35_i();
		return t;
	};
	_proto._Object35_i = function () {
		var t = {};
		this._Object35 = t;
		return t;
	};
	_proto._TweenItem6_i = function () {
		var t = new egret.tween.TweenItem();
		this._TweenItem6 = t;
		t.paths = [this._Set36_i(),this._To2_i()];
		return t;
	};
	_proto._Set36_i = function () {
		var t = new egret.tween.Set();
		t.props = this._Object36_i();
		return t;
	};
	_proto._Object36_i = function () {
		var t = {};
		this._Object36 = t;
		return t;
	};
	_proto._To2_i = function () {
		var t = new egret.tween.To();
		t.duration = 500;
		t.ease = "sineIn";
		t.props = this._Object37_i();
		return t;
	};
	_proto._Object37_i = function () {
		var t = {};
		this._Object37 = t;
		return t;
	};
	_proto.fightGroup_i = function () {
		var t = new eui.Group();
		this.fightGroup = t;
		t.horizontalCenter = 0;
		t.verticalCenter = 50;
		t.visible = false;
		t.elementsContent = [this.img0_i(),this.img1_i(),this.img2_i(),this.img3_i()];
		return t;
	};
	_proto.img0_i = function () {
		var t = new eui.Image();
		this.img0 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-fighting_json.sprite-fighting003";
		t.verticalCenter = 0;
		return t;
	};
	_proto.img1_i = function () {
		var t = new eui.Image();
		this.img1 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-fighting_json.sprite-fighting001";
		t.verticalCenter = 0;
		return t;
	};
	_proto.img2_i = function () {
		var t = new eui.Image();
		this.img2 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-fighting_json.sprite-fighting000";
		t.verticalCenter = 0;
		return t;
	};
	_proto.img3_i = function () {
		var t = new eui.Image();
		this.img3 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-fighting_json.sprite-fighting002";
		t.verticalCenter = 0;
		return t;
	};
	_proto.you_i = function () {
		var t = new Soldier();
		this.you = t;
		t.scaleX = 1;
		t.scaleY = 1;
		t.skinName = "SoldierSkin";
		t.x = 225;
		t.y = 759;
		return t;
	};
	_proto.cmp_i = function () {
		var t = new Soldier();
		this.cmp = t;
		t.scaleX = 1;
		t.scaleY = 1;
		t.skinName = "SoldierSkin";
		t.x = 225;
		t.y = 405;
		return t;
	};
	return ResultSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/SelectSkin.exml'] = window.SelectSkin = (function (_super) {
	__extends(SelectSkin, _super);
	function SelectSkin() {
		_super.call(this);
		this.skinParts = ["backImg","on","off"];
		
		this.elementsContent = [this.backImg_i(),this.on_i(),this.off_i()];
		this.states = [
			new eui.State ("up",
				[
					new eui.SetProperty("off","visible",false)
				])
			,
			new eui.State ("down",
				[
					new eui.SetProperty("off","visible",false)
				])
			,
			new eui.State ("disabled",
				[
					new eui.SetProperty("off","visible",false)
				])
			,
			new eui.State ("upAndSelected",
				[
					new eui.SetProperty("on","visible",false)
				])
			,
			new eui.State ("downAndSelected",
				[
					new eui.SetProperty("on","visible",false)
				])
			,
			new eui.State ("disabledAndSelected",
				[
					new eui.SetProperty("on","visible",false)
				])
		];
	}
	var _proto = SelectSkin.prototype;

	_proto.backImg_i = function () {
		var t = new eui.Image();
		this.backImg = t;
		t.horizontalCenter = 0;
		t.source = "spxiu_json.spxiu005";
		t.verticalCenter = 0;
		return t;
	};
	_proto.on_i = function () {
		var t = new eui.Image();
		this.on = t;
		t.left = 0;
		t.source = "spxiu_json.spxiu009";
		t.verticalCenter = 0;
		return t;
	};
	_proto.off_i = function () {
		var t = new eui.Image();
		this.off = t;
		t.right = 0;
		t.source = "spxiu_json.spxiu006";
		t.verticalCenter = 0;
		return t;
	};
	return SelectSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/SetPanelSkin.exml'] = window.SetPanelSkin = (function (_super) {
	__extends(SetPanelSkin, _super);
	function SetPanelSkin() {
		_super.call(this);
		this.skinParts = ["back","betHistory","help","sound","backHome"];
		
		this.height = 1136;
		this.width = 640;
		this.elementsContent = [this.back_i(),this._Group3_i()];
	}
	var _proto = SetPanelSkin.prototype;

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
	_proto._Group3_i = function () {
		var t = new eui.Group();
		t.height = 300;
		t.right = 20;
		t.top = 100;
		t.width = 200;
		t.elementsContent = [this._Rect1_i(),this._Rect2_i(),this._Group2_i()];
		return t;
	};
	_proto._Rect1_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.ellipseWidth = 10;
		t.fillColor = 0xd37d20;
		t.left = 0;
		t.right = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 0;
		t.x = -303;
		t.y = -167;
		return t;
	};
	_proto._Rect2_i = function () {
		var t = new eui.Rect();
		t.bottom = 5;
		t.ellipseWidth = 10;
		t.fillColor = 0x5d3f36;
		t.left = 5;
		t.right = 5;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 5;
		t.x = -298;
		t.y = -162;
		return t;
	};
	_proto._Group2_i = function () {
		var t = new eui.Group();
		t.bottom = 5;
		t.left = 5;
		t.right = 5;
		t.scaleX = 1;
		t.scaleY = 1;
		t.top = 5;
		t.x = -298;
		t.y = -162;
		t.layout = this._VerticalLayout1_i();
		t.elementsContent = [this.betHistory_i(),this.help_i(),this._Group1_i(),this.backHome_i()];
		return t;
	};
	_proto._VerticalLayout1_i = function () {
		var t = new eui.VerticalLayout();
		t.gap = 15;
		t.horizontalAlign = "center";
		t.paddingTop = 15;
		t.verticalAlign = "top";
		return t;
	};
	_proto.betHistory_i = function () {
		var t = new eui.Group();
		this.betHistory = t;
		t.x = 5;
		t.y = 6;
		t.elementsContent = [this._Label1_i(),this._Rect3_i()];
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		t.height = 50;
		t.horizontalCenter = 0;
		t.text = "投注记录";
		t.textAlign = "center";
		t.textColor = 0xc19251;
		t.verticalAlign = "middle";
		t.verticalCenter = 0;
		t.width = 180;
		return t;
	};
	_proto._Rect3_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.height = 2;
		t.horizontalCenter = 0;
		t.percentWidth = 80;
		return t;
	};
	_proto.help_i = function () {
		var t = new eui.Group();
		this.help = t;
		t.x = 15;
		t.y = 16;
		t.elementsContent = [this._Label2_i(),this._Rect4_i()];
		return t;
	};
	_proto._Label2_i = function () {
		var t = new eui.Label();
		t.height = 50;
		t.horizontalCenter = 0;
		t.text = "玩法介绍";
		t.textAlign = "center";
		t.textColor = 0xC19251;
		t.verticalAlign = "middle";
		t.verticalCenter = 0;
		t.width = 180;
		return t;
	};
	_proto._Rect4_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.height = 2;
		t.horizontalCenter = 0;
		t.percentWidth = 80;
		return t;
	};
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.width = 180;
		t.x = 25;
		t.y = 26;
		t.elementsContent = [this._Label3_i(),this._Rect5_i(),this.sound_i()];
		return t;
	};
	_proto._Label3_i = function () {
		var t = new eui.Label();
		t.height = 50;
		t.left = 10;
		t.text = "音乐";
		t.textAlign = "center";
		t.textColor = 0xC19251;
		t.verticalAlign = "middle";
		t.verticalCenter = 0;
		t.width = 90;
		return t;
	};
	_proto._Rect5_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.height = 2;
		t.horizontalCenter = 0;
		t.percentWidth = 80;
		return t;
	};
	_proto.sound_i = function () {
		var t = new eui.ToggleSwitch();
		this.sound = t;
		t.label = "";
		t.right = 15;
		t.scaleX = 0.7;
		t.scaleY = 0.7;
		t.skinName = "SelectSkin";
		t.verticalCenter = 0;
		return t;
	};
	_proto.backHome_i = function () {
		var t = new eui.Group();
		this.backHome = t;
		t.x = 35;
		t.y = 36;
		t.elementsContent = [this._Label4_i(),this._Rect6_i()];
		return t;
	};
	_proto._Label4_i = function () {
		var t = new eui.Label();
		t.height = 50;
		t.horizontalCenter = 0;
		t.text = "返回首页";
		t.textAlign = "center";
		t.textColor = 0xC19251;
		t.verticalAlign = "middle";
		t.verticalCenter = 0;
		t.width = 180;
		return t;
	};
	_proto._Rect6_i = function () {
		var t = new eui.Rect();
		t.bottom = 0;
		t.fillAlpha = 0.5;
		t.height = 2;
		t.horizontalCenter = 0;
		t.percentWidth = 80;
		return t;
	};
	return SetPanelSkin;
})(eui.Skin);generateEUI.paths['resource/eui_skins/GameSkins/ShowResultSkin.exml'] = window.ShowResultSkin = (function (_super) {
	__extends(ShowResultSkin, _super);
	function ShowResultSkin() {
		_super.call(this);
		this.skinParts = ["back","giftImg","gift"];
		
		this.height = 1136;
		this.width = 640;
		this.elementsContent = [this.back_i(),this._Group1_i()];
		this.states = [
			new eui.State ("win",
				[
					new eui.SetProperty("_Image2","source","sprite-bubble-avator_json.sprite-bubble-avator002"),
					new eui.SetProperty("giftImg","verticalCenter",75)
				])
			,
			new eui.State ("lose",
				[
					new eui.SetProperty("_Image1","visible",false),
					new eui.SetProperty("giftImg","verticalCenter",70),
					new eui.SetProperty("giftImg","source","sprite-bubble-title_json.sprite-bubble-title007"),
					new eui.SetProperty("_Label1","visible",false),
					new eui.SetProperty("gift","visible",false)
				])
			,
			new eui.State ("zero",
				[
					new eui.SetProperty("_Image1","visible",false),
					new eui.SetProperty("giftImg","bottom",0),
					new eui.SetProperty("giftImg","source","sprite-bubble-title_json.sprite-bubble-title006"),
					new eui.SetProperty("_Label1","visible",false),
					new eui.SetProperty("gift","visible",false)
				])
		];
	}
	var _proto = ShowResultSkin.prototype;

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
	_proto._Group1_i = function () {
		var t = new eui.Group();
		t.height = 343;
		t.horizontalCenter = 0;
		t.verticalCenter = 0;
		t.elementsContent = [this._Image1_i(),this._Image2_i(),this._Image3_i(),this._Image4_i(),this.giftImg_i(),this._Label1_i(),this.gift_i()];
		return t;
	};
	_proto._Image1_i = function () {
		var t = new eui.Image();
		this._Image1 = t;
		t.bottom = -15;
		t.horizontalCenter = 0;
		t.source = "show_png";
		return t;
	};
	_proto._Image2_i = function () {
		var t = new eui.Image();
		this._Image2 = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-bubble-avator_json.sprite-bubble-avator002";
		t.verticalCenter = 0;
		return t;
	};
	_proto._Image3_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = -20;
		t.source = "sprite-bubble-avator_json.sprite-bubble-avator000";
		t.verticalCenter = 20;
		return t;
	};
	_proto._Image4_i = function () {
		var t = new eui.Image();
		t.horizontalCenter = 0;
		t.source = "sprite-game-soldier_json.sprite-game-soldier000";
		t.verticalCenter = 0;
		return t;
	};
	_proto.giftImg_i = function () {
		var t = new eui.Image();
		this.giftImg = t;
		t.horizontalCenter = 0;
		t.scaleX = 1;
		t.scaleY = 1;
		t.source = "sprite-bubble-title_json.sprite-bubble-title000";
		return t;
	};
	_proto._Label1_i = function () {
		var t = new eui.Label();
		this._Label1 = t;
		t.bottom = 0;
		t.horizontalCenter = -70;
		t.size = 30;
		t.text = "获得奖金：";
		t.textAlign = "center";
		t.textColor = 0xe2c27c;
		t.verticalAlign = "middle";
		return t;
	};
	_proto.gift_i = function () {
		var t = new eui.Label();
		this.gift = t;
		t.bottom = 0;
		t.horizontalCenter = 70;
		t.text = "200金豆";
		t.textColor = 0xffc823;
		return t;
	};
	return ShowResultSkin;
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