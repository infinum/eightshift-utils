import { eightshiftConfig } from '@eightshift/frontend-libs-tailwind/webpack/index.mjs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * This is a main entrypoint for Webpack config.
 * All the settings are pulled from node_modules/@eightshift/frontend-libs/webpack.
 * We are loading mostly used configuration but you can always override or turn off the default setup and provide your own.
 * Please referer to Eightshift-libs wiki for details.
 */
export default (_, argv) => {
	const projectConfig = {
		config: {
			projectDir: __dirname, // Current project directory absolute path.
			projectPath: 'wp-content/plugins/eightshift-utils', // Project path relative to project root.
		},
	};

	// Generate webpack config for this project using options object.
	const project = eightshiftConfig(argv.mode, projectConfig);

	return{
		// Load all projects config from eightshift-frontend-libs.
		...project,

		output: {
			// Load all output config from eightshift-frontend-libs.
			...project.output,
			library: 'EightshiftUtils',
		},

		entry: {
			...project.entry,
			applicationAdmin: path.join(projectConfig.config.projectDir, '/src/Blocks/assets/application-admin.js'),
		},
	};
};
