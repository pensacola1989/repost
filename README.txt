请注意以下几点。
	1.我们TAE现在的环境暂时还不支持非DEBGG模式的ThinkPHP。Bug已经修复。待发布。
	  模板引擎请使用内置的Smarty4j
		即修改配置
		'TMPL_ENGINE_TYPE'      =>  'Smarty4j',
		系统默认使用，如果报错。请检查这个配置项

	2.如果系统报错。请查看日志文件。要是遇到
		函数(xxxxxx)不存在
		请查看报错的行，这个异常的原因是smarty把这个代码当做PHP来处理。如果
		代码是在CSS或者是Javascript中请使用。{literal}标签来进行处理

	3. 支持PDO连接
	4. Smarty使用注意：
	 {foreach from=$data item="i"} 标签中from=$data不能写成from="" 否则就知道把整个数组打印了。
	 include使用方法：
	 {include file="../Public/header.html"}

	5. 显示暂时不支持的函数。我们SDK已修复。如果要使用，请使用function_exists进行容错
		strip_whitespace；
		debug_print_backtrace；
		php_strip_whitespace；
		class_alias。
	6.修复bug：
		quercus支持魔法函数__callStatic时区分大小写，原生php不区分。
	7. 解决部分地方提示C函数不可用问题
	8. 如果表单使用get的方式。请注意需要使用hidden域（<input type="hidden"）加入所需要的参数包括。请求的控制器和Action
