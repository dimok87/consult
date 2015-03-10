var Chat = React.createClass({
   loadMessagesFromServer: function() {
     $.ajax({
         url: this.props.getUrl,
         dataType: 'json',
         type: 'post',
         data: {client: this.props.client},
         success: function(data) {
           this.setState({
              messages: data
           });
         }.bind(this),
         error: function(xhr, status, err) {
           console.error(this.props.url, status, err.toString());
         }.bind(this)
      }); 
   },
   getInitialState: function() {
      return {messages: []};
   },
   componentDidMount: function() {
      this.loadMessagesFromServer();
      setInterval(this.loadMessagesFromServer, this.props.pollInterval);
   },
   handleMessageSubmit: function(message) {
      var today = new Date();
      var dd = today.getDate();
      var mm = today.getMonth() + 1; //January is 0!
      var yyyy = today.getFullYear();
      var hh = today.getHours() + 1;
      var ii = today.getMinutes();
      if(dd < 10){
          dd = '0' + dd;
      } 
      if(mm<10){
          mm = '0' + mm;
      }
      if(hh<10){
          hh = '0' + hh;
      }
      if(ii < 10){
          ii = '0' + ii;
      }
      today = dd+'.'+mm+'.'+yyyy + ' ' + hh + ':' + ii;
      var messages = this.state.messages.concat([{message: message, isToClient : false, created: today}]);
      this.setState({messages : messages});
      $.ajax({
         url: this.props.sendUrl,
         dataType: 'json',
         type: 'POST',
         data: {message: message, client: this.props.client, consultant: this.props.consultant},
         success: function(data) {
            this.setState({messages: data});
         }.bind(this),
         error: function(xhr, status, err) {
           console.error(this.props.url, status, err.toString());
         }.bind(this)
      });
   },
   render: function() {
      return (    
         <div className="row">
            <div className="col-lg-12">
               <Messages data={this.state.messages} />
               <MessageForm onMessageSubmit={this.handleMessageSubmit} />
            </div>
         </div>
      );
   }
});

var Messages = React.createClass({
   render: function() {
      var messagesNodes = this.props.data.map(function(message) {
         return (
           <Message message={message}></Message>
         );
      });
      return (
         <div className="list-group">
         {messagesNodes}
         </div>
      );
   }
});

var MessageForm = React.createClass({
   handleSubmit: function(e) {
      e.preventDefault();
      var text = this.refs.text.getDOMNode().value.trim();
      if (!text) {
        return;
      }
      this.props.onMessageSubmit(text);
      this.refs.text.getDOMNode().value = '';
   },
   render: function() {
      return (
        <form className="messageForm form-group" onSubmit={this.handleSubmit}>
         <textarea type="text" ref="text" className="form-control" placeholder="Ваше сообщение..." />
         <input type="submit" className="btn btn-primary" value="Отправить" />
        </form>
      );
   }
});

var Message = React.createClass({
   render: function() {
      var cx = React.addons.classSet;
      var classes = cx({
        'list-group-item': true,
        'list-group-item-success': this.props.message.isToClient,
        'list-group-item-danger': !this.props.message.isToClient
      });
      return (
         <li className={classes}>{this.props.message.message}<span className="badge">{this.props.message.created}</span></li>
      );
   }
});

React.render(
        <Chat sendUrl={document.getElementById('chat').getAttribute('data-send')} 
              getUrl={document.getElementById('chat').getAttribute('data-get')}
              client={document.getElementById('chat').getAttribute('data-client')} 
              consultant={document.getElementById('chat').getAttribute('data-consultant')} 
              pollInterval={2000} />, 
        document.getElementById('chat')
);