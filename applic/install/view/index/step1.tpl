<div style="text-align: left; height: 400px; overflow-y: scroll; line-height: 30px;">
EasyWcms V1.0 安装协议<br>
版权所有Copyright © 2018-2019 by EasyWcms 保留所有权利。<br>
<br>
感谢您选择EasyWcms，希望我们的努力能为您提供一个简单、强大的站点解决方案。<br>
<br>
用户须知：本协议是您与 EasyWcms 之间关于您使用本产品及服务的法律协议。无论您是个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，包括免除或者限制 EasyWcms 责任的免责条款及对您的权利限制。请您审阅并接受或不接受本服务条款。如您不同意本服务条款及/或EasyWcms随时对其的修改，您应不使用或主动取消本产品。否则，您的任何对EasyWcms的相关服务的注册、登陆、下载、查看等使用行为将被视为您对本服务条款全部的完全接受，包括接受EasyWcms对服务条款随时所做的任何修改。
<br><br>
本服务条款一旦发生变更, EasyWcms将在产品官网上公布修改内容。修改后的服务条款一旦在网站公布即有效代替原来的服务条款。您可随时登陆官网查阅最新版服务条款。如果您选择接受本条款，即表示您同意接受协议各项条件的约束。如果您不同意本服务条款，则不能获得使用本服务的权利。您若有违反本条款规定，EasyWcms有权随时中止或终止您对本产品的使用资格并保留追究相关法律责任的权利。
<br><br>
在理解、同意、并遵守本协议的全部条款后，方可开始使用本产品。您也可能与EasyWcms直接签订另一书面协议，以补充或者取代本协议的全部或者任何部分。
<br><br>
EasyWcms拥有本产品的全部知识产权，包括商标和著作权。本软件只供许可协议，并非出售。EasyWcms只允许您在遵守本协议各项条款的情况下复制、下载、安装、使用或者以其他方式受益于本软件的功能或者知识产权。
<br><br>
<strong>有限担保和免责声明 </strong>
<br>
本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。 <br>
用户出于自愿而使用本软件，您必须了解使用本软件的风险，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。 <br>
EasyWcms不对使用本软件构建的网站中的文章或信息承担责任。 
    
<br><br>  
EasyWcms 遵循Apache Licence2开源协议，并且免费使用（但不包括其衍生产品、插件或者服务）。Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，允许代码修改，再作为开源或商业软件发布。需要满足的条件：<br>
1． 需要给用户一份Apache Licence ；<br>
2． 如果你修改了代码，需要在被修改的文件中说明；<br>
3． 在延伸的代码中（修改和有源代码衍生的代码中）需要带有原来代码中的协议，商标，专利声明和其他原来作者规定需要包含的说明；<br>
4． 如果再发布的产品中包含一个Notice文件，则在Notice文件中需要带有本协议内容。你可以在Notice中增加自己的许可，但不可以表现为对Apache Licence构成更改。
</div>
<script>
easy.define(function(){
    
    $('.footer label').addClass('block')
    $('#tongyi').removeAttr("checked");
    $('#atongyi').removeAttr("href");
    
    $('#tongyi').click(function(){
        var checked = $(this).attr("checked");
        if(checked == 'checked'){
            $(this).removeAttr("checked");
            $('#atongyi').removeAttr("href");
        }else{
            $(this).attr("checked",'checked');
            $('#atongyi').attr("href","{:url('/Install?step=2')}");
        }
    });
});
</script>