import ReactDOM from 'react-dom/client'
import App from './App.tsx'
import './index.css'
import ReduxProvider from '@redux/provider'
import { BrowserRouter as Router } from 'react-router-dom'

ReactDOM.createRoot(document.getElementById('root')!).render(
  <ReduxProvider>
    <Router>
      <App />
    </Router>
  </ReduxProvider>,
)
