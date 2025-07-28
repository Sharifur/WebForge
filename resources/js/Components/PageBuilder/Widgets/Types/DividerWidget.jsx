import React from 'react';

const DividerWidget = ({ 
  style = 'solid', 
  width = '1px', 
  color = '#e5e7eb',
  marginTop = '20px',
  marginBottom = '20px'
}) => {
  return (
    <hr 
      style={{
        borderStyle: style,
        borderWidth: width,
        borderColor: color,
        marginTop,
        marginBottom,
        borderTopWidth: width,
        borderLeftWidth: 0,
        borderRightWidth: 0,
        borderBottomWidth: 0
      }}
      className="w-full"
    />
  );
};

export default DividerWidget;