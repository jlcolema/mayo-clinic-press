// Add alignment to core group block
function changeHeadingTypography(settings, name) {
  if (name !== 'core/heading') {
    return settings;
  }

  return settings;
}

wp.hooks.addFilter(
  'blocks.registerBlockType',
  'mayo',
  changeHeadingTypography
);
