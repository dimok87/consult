var Visit = React.createClass({
   loadPagesFromServer: function() {
     $.ajax({
         url: this.props.getUrl,
         dataType: 'json',
         type: 'get',
         success: function(data) {
           this.setState({
              pages: data
           });
         }.bind(this),
         error: function(xhr, status, err) {
           console.error(this.props.url, status, err.toString());
         }.bind(this)
      }); 
   },
   getInitialState: function() {
      return {pages: []};
   },
   componentDidMount: function() {
      this.loadPagesFromServer();
      setInterval(this.loadPagesFromServer, this.props.pollInterval);
   },
   render: function() {
      var pagesNodes = this.state.pages.map(function(page) {
         return (
           <Page data={page}></Page>
         );
      });
      return (    
         <div className="list-group">
            {pagesNodes}
         </div>
      );
   }
});

var Page = React.createClass({
   render: function() {
      return (
         <li className="list-group-item">{this.props.data.title} - {this.props.data.url}<span className="badge">{this.props.data.created}</span></li>
      );
   }
});

React.render(
        <Visit getUrl={document.getElementById('visit').getAttribute('rel')}
              pollInterval={2000} />, 
        document.getElementById('visit')
);