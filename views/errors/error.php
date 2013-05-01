<?php namespace Hanariu;

// Unique error identifier
$error_id = uniqid('error');

?>
<style type="text/css">
#hanariu_error { background: #ddd; font-size: 1em; font-family:sans-serif; text-align: left; color: #111; }
#hanariu_error h1,
#hanariu_error h2 { margin: 0; padding: 1em; font-size: 1em; font-weight: normal; background: #911; color: #fff; }
	#hanariu_error h1 a,
	#hanariu_error h2 a { color: #fff; }
#hanariu_error h2 { background: #222; }
#hanariu_error h3 { margin: 0; padding: 0.4em 0 0; font-size: 1em; font-weight: normal; }
#hanariu_error p { margin: 0; padding: 0.2em 0; }
#hanariu_error a { color: #1b323b; }
#hanariu_error pre { overflow: auto; white-space: pre-wrap; }
#hanariu_error table { width: 100%; display: block; margin: 0 0 0.4em; padding: 0; border-collapse: collapse; background: #fff; }
	#hanariu_error table td { border: solid 1px #ddd; text-align: left; vertical-align: top; padding: 0.4em; }
#hanariu_error section.content { padding: 0.4em 1em 1em; overflow: hidden; }
#hanariu_error pre.source { margin: 0 0 1em; padding: 0.4em; background: #fff; border: dotted 1px #b7c680; line-height: 1.2em; }
	#hanariu_error pre.source span.line { display: block; }
	#hanariu_error pre.source span.highlight { background: #f0eb96; }
		#hanariu_error pre.source span.line span.number { color: #666; }
#hanariu_error ol.trace { display: block; margin: 0 0 0 2em; padding: 0; list-style: decimal; }
	#hanariu_error ol.trace li { margin: 0; padding: 0; }
.js .collapsed { display: none; }
</style>
<script type="text/javascript">
document.documentElement.className = document.documentElement.className + ' js';
function koggle(elem)
{
	elem = document.getElementById(elem);

	if (elem.style && elem.style['display'])
		// Only works with the "style" attr
		var disp = elem.style['display'];
	else if (elem.currentStyle)
		// For MSIE, naturally
		var disp = elem.currentStyle['display'];
	else if (window.getComputedStyle)
		// For most other browsers
		var disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');

	// Toggle the state of the "display" style
	elem.style.display = disp == 'block' ? 'none' : 'block';
	return false;
}
</script>
<section id="hanariu_error">
	<h1><span class="type"><?php echo $class ?> [ <?php echo $code ?> ]:</span> <span class="message"><?php echo \Hanariu\Utils::chars($message) ?></span></h1>
	<section id="<?php echo $error_id ?>" class="content">
		<p><span class="file"><?php echo \Hanariu\Debug::path($file) ?> [ <?php echo $line ?> ]</span></p>
		<?php echo \Hanariu\Debug::source($file, $line) ?>
		<ol class="trace">
		<?php foreach (\Hanariu\Debug::trace($trace) as $i => $step): ?>
			<li>
				<p>
					<span class="file">
						<?php if ($step['file']): $source_id = $error_id.'source'.$i; ?>
							<a href="#<?php echo $source_id ?>" onclick="return koggle('<?php echo $source_id ?>')"><?php echo \Hanariu\Debug::path($step['file']) ?> [ <?php echo $step['line'] ?> ]</a>
						<?php else: ?>
							{<?php echo __('PHP internal call') ?>}
						<?php endif ?>
					</span>
					&raquo;
					<?php echo $step['function'] ?>(<?php if ($step['args']): $args_id = $error_id.'args'.$i; ?><a href="#<?php echo $args_id ?>" onclick="return koggle('<?php echo $args_id ?>')"><?php echo __('arguments') ?></a><?php endif ?>)
				</p>
				<?php if (isset($args_id)): ?>
				<section id="<?php echo $args_id ?>" class="collapsed">
					<table cellspacing="0">
					<?php foreach ($step['args'] as $name => $arg): ?>
						<tr>
							<td><code><?php echo $name ?></code></td>
							<td><pre><?php echo \Hanariu\Debug::dump($arg) ?></pre></td>
						</tr>
					<?php endforeach ?>
					</table>
				</section>
				<?php endif ?>
				<?php if (isset($source_id)): ?>
					<pre id="<?php echo $source_id ?>" class="source collapsed"><code><?php echo $step['source'] ?></code></pre>
				<?php endif ?>
			</li>
			<?php unset($args_id, $source_id); ?>
		<?php endforeach ?>
		</ol>
	</section>
	<h2><a href="#<?php echo $env_id = $error_id.'environment' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Environment') ?></a></h2>
	<section id="<?php echo $env_id ?>" class="content collapsed">
		<?php $included = \get_included_files() ?>
		<h3><a href="#<?php echo $env_id = $error_id.'environment_included' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Included files') ?></a> (<?php echo count($included) ?>)</h3>
		<section id="<?php echo $env_id ?>" class="collapsed">
			<table cellspacing="0">
				<?php foreach ($included as $file): ?>
				<tr>
					<td><code><?php echo \Hanariu\Debug::path($file) ?></code></td>
				</tr>
				<?php endforeach ?>
			</table>
		</section>
		<?php $included = \get_loaded_extensions() ?>
		<h3><a href="#<?php echo $env_id = $error_id.'environment_loaded' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Loaded extensions') ?></a> (<?php echo count($included) ?>)</h3>
		<section id="<?php echo $env_id ?>" class="collapsed">
			<table cellspacing="0">
				<?php foreach ($included as $file): ?>
				<tr>
					<td><code><?php echo \Hanariu\Debug::path($file) ?></code></td>
				</tr>
				<?php endforeach ?>
			</table>
		</section>
		<?php foreach (array('_SESSION', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER') as $var): ?>
		<?php if (empty($GLOBALS[$var]) OR ! \is_array($GLOBALS[$var])) continue ?>
		<h3><a href="#<?php echo $env_id = $error_id.'environment'.\strtolower($var) ?>" onclick="return koggle('<?php echo $env_id ?>')">$<?php echo $var ?></a></h3>
		<section id="<?php echo $env_id ?>" class="collapsed">
			<table cellspacing="0">
				<?php foreach ($GLOBALS[$var] as $key => $value): ?>
				<tr>
					<td><code><?php echo \Hanariu\Utils::chars($key) ?></code></td>
					<td><pre><?php echo \Hanariu\Debug::dump($value) ?></pre></td>
				</tr>
				<?php endforeach ?>
			</table>
		</section>
		<?php endforeach ?>
	</section>
</section>
