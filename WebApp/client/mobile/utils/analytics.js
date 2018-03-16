import 'autotrack/autotrack.js'
/**
 * google analytics 统计分析
 */

/* eslint-disable */
window.ga = window.ga || function () { (ga.q=ga.q||[]).push(arguments) }
ga.l = +new Date
ga('create', 'UA-92787655-1', 'auto')

// Only require the plugins you've imported above.
ga('require', 'eventTracker')
ga('require', 'outboundLinkTracker')
ga('require', 'urlChangeTracker')

ga('send', 'pageview')
/* eslint-endable */

export default ga